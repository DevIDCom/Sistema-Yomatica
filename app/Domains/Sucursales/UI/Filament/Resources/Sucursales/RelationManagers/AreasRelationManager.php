<?php

namespace App\Domains\Sucursales\UI\Filament\Resources\Sucursales\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AreasRelationManager extends RelationManager
{
    protected static string $relationship = 'areas';

    protected static ?string $title = 'Áreas';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del área')
                    ->placeholder('Ej. Cocina, Almacén, Ventas')
                    ->required()
                    ->maxLength(255),

                TextInput::make('floor_level')
                    ->label('Nivel / Piso')
                    ->placeholder('Ej. 1, 2, 3')
                    ->numeric()
                    ->integer()
                    ->required()
                    ->validationMessages([
                        'integer' => 'Solo puedes capturar números enteros.',
                        'numeric' => 'El valor debe ser un número.',
                        'required'=> 'El Piso es obligatorio.',
                    ])

                    ->maxLength(50),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('floor_level')
                    ->label('Nivel')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('tickets_count')
                    ->label('Tickets')
                    ->counts('tickets')
                    ->badge(),
            ])
            ->defaultSort('name')
            ->headerActions([
                CreateAction::make()->label('Nueva área'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin áreas')
            ->emptyStateDescription('Registra las áreas de esta sucursal.');
    }
}
