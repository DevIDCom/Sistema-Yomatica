<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Tables;

use App\Domains\Proyectos\Domain\StateMachines\Project\ProjectState;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProyectosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Nombre del Proyecto')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('client.business_name')
                ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('quote.uuid')
                    ->label('Cotización')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'label') ? $state->label() : $state)
                    ->color(fn ($state) => is_object($state) && method_exists($state, 'color') ? $state->color() : 'gray')
                    ->icon(fn ($state) => is_object($state) && method_exists($state, 'icon') ? $state->icon() : null)
                    ->sortable(),

                TextColumn::make('total_budget')
                    ->label('Presupuesto Total')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('financial_progress_percentage')
                    ->label('Avance Financiero')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'info',
                        $state > 0 => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estatus')
                    ->options(ProjectState::getSelectOptions()),

                SelectFilter::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'business_name')
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
