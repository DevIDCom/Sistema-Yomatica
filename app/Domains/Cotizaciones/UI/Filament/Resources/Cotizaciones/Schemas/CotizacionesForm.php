<?php

namespace App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Schemas;

use App\Domains\Cotizaciones\Domain\StateMachines\Quote\QuoteState;
use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CotizacionesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General de la Cotización')
                    ->description('Datos de identificación, cliente y jerarquía de versión')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        TextInput::make('uuid')
                            ->label('UUID Identificador')
                            ->default(fn () => (string) Str::uuid())
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->columnSpan(4),

                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'business_name')
                            ->default(fn () => request()->query('cliente_id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('branch_id', null);
                                $set('parent_id', null);
                            })
                            ->columnSpan(4),

                        Select::make('branch_id')
                            ->label('Sucursal')
                            ->relationship(
                                name: 'branch',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, callable $get) => $query
                                    ->where('client_id', $get('client_id')),
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable()
                            ->disabled(fn (callable $get) => blank($get('client_id')))
                            ->placeholder(fn (callable $get) => blank($get('client_id'))
                                ? 'Selecciona primero el cliente'
                                : 'Sin sucursal')
                            ->columnSpan(4),

                        Select::make('parent_id')
                            ->label('Cotización Padre / Versión Previa')
                            ->relationship(
                                name: 'parentQuote',
                                titleAttribute: 'uuid',
                                modifyQueryUsing: fn (Builder $query, callable $get, ?CpqQuote $record) => $query
                                    ->where('client_id', $get('client_id'))
                                    ->when($record, fn (Builder $q) => $q->whereKeyNot($record->getKey()))
                                    ->latest('created_at'),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (CpqQuote $record) => Str::limit($record->uuid, 13)
                                    . ' — ' . $record->created_at?->format('d/m/Y')
                                    . ' — $' . number_format((float) $record->total, 2)
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable()
                            ->disabled(fn (callable $get) => blank($get('client_id')))
                            ->placeholder(fn (callable $get) => blank($get('client_id'))
                                ? 'Selecciona primero el cliente'
                                : 'Sin versión previa')
                            ->columnSpan(6),
                        Select::make('status')
                            ->label('Estatus')
                            ->options(QuoteState::getSelectOptions())
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->placeholder('Borrador')
                            ->helperText('Se modifica desde la acción "Cambiar estatus" del listado.')
                            ->columnSpan(6),
                    ]),

                Section::make('Moneda y Parámetros Financieros')
                    ->description('Configuración de divisa, tipo de cambio y totales')
                    ->icon('heroicon-o-currency-dollar')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        Select::make('currency')
                            ->label('Moneda')
                            ->options([
                                'MXN' => 'MXN - Peso Mexicano',
                                'USD' => 'USD - Dólar Estadounidense',
                                'EUR' => 'EUR - Euro',
                            ])
                            ->default('MXN')
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, ?string $state) {
                                if ($state === 'MXN') {
                                    $set('exchange_rate', 1.0000);
                                }
                            })
                            ->columnSpan(4),

                        TextInput::make('exchange_rate')
                            ->label('Tipo de Cambio')
                            ->placeholder('Ej. 17.2500')
                            ->numeric()
                            ->step('0.0001')
                            ->minValue(0.0001)
                            ->default(1.0000)
                            ->required()
                            ->disabled(fn (callable $get) => $get('currency') === 'MXN')
                            ->dehydrated()
                            ->validationMessages([
                                'min' => 'El tipo de cambio debe ser mayor a cero.',
                            ])
                            ->columnSpan(4),

                        TextInput::make('total')
                            ->label('Monto Total')
                            ->placeholder('Ej. 45000.00')
                            ->numeric()
                            ->prefix('$')
                            ->step('0.01')
                            ->minValue(0)
                            ->default(0.00)
                            ->required()
                            ->columnSpan(4),
                    ]),
            ]);
    }
}
