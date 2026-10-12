<?php

namespace App\Domains\Tickets\UI\Filament\Resources\Tickets\Tables;

use App\Domains\Tickets\Domain\StateMachines\Ticket\TicketState;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Folio')
                    ->sortable()
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('client.business_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('technician.name')
                    ->label('Técnico Asignado')
                    ->placeholder('Sin asignar')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'label') ? $state->label() : $state)
                    ->color(fn ($state) => is_object($state) && method_exists($state, 'color') ? $state->color() : 'gray')
                    ->icon(fn ($state) => is_object($state) && method_exists($state, 'icon') ? $state->icon() : null)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estatus')
                    ->options(TicketState::getSelectOptions()),

                SelectFilter::make('assigned_tech_id')
                    ->label('Técnico')
                    ->relationship('technician', 'name')
                    ->searchable()
                    ->preload(),
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
