<?php

namespace App\Domains\Tickets\UI\Filament\Resources\Tickets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $title = 'Materiales e Insumos';

    protected static string|null|\BackedEnum $icon = 'heroicon-o-wrench-screwdriver';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Producto / Material')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('warehouse_id')
                    ->label('Almacén / Bodega')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('quantity_used')
                    ->label('Cantidad Utilizada')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),

                TextInput::make('unit_cost')
                    ->label('Costo Unitario')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn ($record): ?string => $record->product?->name)
            ->columns([
                TextColumn::make('product.name')
                    ->label('Producto / Material')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->sortable(),

                TextColumn::make('quantity_used')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('unit_cost')
                    ->label('Costo Unitario')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('total_cost')
                    ->label('Costo Total')
                    ->money('MXN')
                    ->state(fn ($record) => $record->quantity_used * $record->unit_cost),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar Material'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
