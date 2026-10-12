<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Schemas;

use App\Domains\Cotizaciones\Domain\StateMachines\Quote\Approved;
use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Proyectos\Domain\StateMachines\Project\ProjectState;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProyectosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Proyecto')
                    ->description('Detalles generales y vinculación comercial')
                    ->icon('heroicon-o-briefcase')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        TextInput::make('code')
                            ->label('Código / Folio')
                            ->default(fn () => FsmProject::generateCode())
                            ->required()
                            ->maxLength(50)
                            ->unique(FsmProject::class, 'code', ignoreRecord: true)
                            ->columnSpan(4),

                        TextInput::make('name')
                            ->label('Nombre del proyecto')
                            ->placeholder('Ej: Mantenimiento Zona Norte')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(8),

                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship(name: 'client', titleAttribute: 'business_name')
                            ->default(fn () => request()->query('cliente_id'))
                            ->disabled(fn () => filled(request()->query('cliente_id')))
                            ->dehydrated()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('quote_id', null))
                            ->columnSpan(6),

                        Select::make('quote_id')
                            ->label('Cotización Origen')
                            ->relationship(
                                name: 'quote',
                                titleAttribute: 'uuid',
                                modifyQueryUsing: fn (Builder $query, callable $get) => $query
                                    ->where('client_id', $get('client_id'))
                                    // Solo cotizaciones aprobadas originan proyectos.
                                    ->whereState('status', Approved::class)
                                    ->latest('created_at'),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (CpqQuote $record) => Str::limit($record->uuid, 13)
                                    . ' — ' . $record->created_at?->format('d/m/Y')
                                    . ' — $' . number_format((float) $record->total, 2)
                            )
                            ->default(fn () => request()->query('cotizacion_id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable()
                            ->disabled(fn (callable $get) => blank($get('client_id')))
                            ->placeholder(fn (callable $get) => blank($get('client_id'))
                                ? 'Selecciona primero el cliente'
                                : 'Sin cotización origen')
                            ->helperText('Solo se listan cotizaciones aprobadas de este cliente.')
                            ->columnSpan(6),
                    ]),

                Section::make('Estatus y Control Financiero')
                    ->description('Presupuesto, avance y estado operativo')
                    ->icon('heroicon-o-currency-dollar')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        Select::make('status')
                            ->label('Estado Operativo')
                            ->options(ProjectState::getSelectOptions())
                            ->default('draft')
                            ->native(false)
                            ->required()
                            ->disableOptionWhen(function (string $value, ?FsmProject $record) {
                                if (! $record) {
                                    return $value !== 'draft';
                                }

                                $currentState = (string) $record->status;

                                return $value !== $currentState && ! $record->status->canTransitionTo($value);
                            })
                            ->columnSpan(4),

                        TextInput::make('total_budget')
                            ->label('Presupuesto Total')
                            ->placeholder('Ej. 150000.00')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->default(0.00)
                            ->required()
                            ->columnSpan(4),

                        TextInput::make('financial_progress_percentage')
                            ->label('Avance Financiero')
                            ->placeholder('Ej. 25')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->required()
                            ->columnSpan(4),
                    ]),
            ]);
    }
}
