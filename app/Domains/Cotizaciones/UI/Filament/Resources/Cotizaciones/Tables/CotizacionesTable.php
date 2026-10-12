<?php

namespace App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Tables;

use App\Domains\Cotizaciones\Domain\StateMachines\Quote\Approved;
use App\Domains\Cotizaciones\Domain\StateMachines\Quote\QuoteState;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\ProyectosResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\ModelStates\Exceptions\TransitionNotFound;

class CotizacionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID / Folio')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->limit(13),

                TextColumn::make('client.business_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('branch.name')
                    ->label('Sucursal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('parentQuote.uuid')
                    ->label('Versión Previa')
                    ->limit(10)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Estatus Comercial')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? '—')
                    ->color(fn ($state) => $state?->color() ?? 'gray')
                    ->icon(fn ($state) => $state?->icon())
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Moneda')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('exchange_rate')
                    ->label('T.C.')
                    ->numeric(decimalPlaces: 4)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total')
                    ->label('Monto Total')
                    ->money(fn ($record) => $record->currency ?? 'MXN')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('created_at')
                    ->label('Fecha de Emisión')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estatus')
                    ->options(QuoteState::getSelectOptions()),

                SelectFilter::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'business_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('currency')
                    ->label('Moneda')
                    ->options([
                        'MXN' => 'MXN - Peso Mexicano',
                        'USD' => 'USD - Dólar Estadounidense',
                        'EUR' => 'EUR - Euro',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('cambiarEstatus')
                    ->label('Estatus')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->modalHeading('Cambiar estatus comercial')
                    ->modalDescription(fn ($record) => 'Estatus actual: ' . $record->status->label())
                    ->modalSubmitActionLabel('Actualizar')
                    ->modalWidth('md')
                    ->schema([
                        Select::make('status')
                            ->label('Nuevo estatus')
                            ->options(function ($record) {
                                $labels = QuoteState::getSelectOptions();

                                return collect($record->status->transitionableStates())
                                    ->mapWithKeys(fn (string $name) => [
                                        $name => $labels[$name] ?? $name,
                                    ])
                                    ->all();
                            })
                            ->required()
                            ->native(false)
                            ->helperText('Solo se muestran las transiciones permitidas.'),
                    ])
                    ->visible(fn ($record) => filled($record->status->transitionableStates()))
                    ->action(function ($record, array $data) {
                        try {
                            $record->status->transitionTo($data['status']);

                            Notification::make()
                                ->title('Estatus actualizado')
                                ->body('La cotización ahora está en: ' . $record->fresh()->status->label())
                                ->success()
                                ->send();
                        } catch (TransitionNotFound $e) {
                            Notification::make()
                                ->title('Transición no permitida')
                                ->body('No existe una transición registrada hacia ese estatus.')
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('crearProyecto')
                    ->label('Proyecto')
                    ->icon('heroicon-o-briefcase')
                    ->color('success')
                    ->visible(fn ($record) => $record->status->equals(Approved::class))
                    ->url(fn ($record) => ProyectosResource::getUrl('create', [
                        'cliente_id'    => $record->client_id,
                        'cotizacion_id' => $record->id,
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
