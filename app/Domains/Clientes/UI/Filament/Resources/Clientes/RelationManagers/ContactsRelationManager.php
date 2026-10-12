<?php

namespace App\Domains\Clientes\UI\Filament\Resources\Clientes\RelationManagers;

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

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    protected static ?string $title = 'Contactos';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->placeholder('Ej. Juan Pérez')
                    ->required()
                    ->maxLength(255),

                TextInput::make('role_title')
                    ->label('Puesto')
                    ->placeholder('Ej. Gerente de Ventas')
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->placeholder('Ej. juan.perez@empresa.com')
                    ->email()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->placeholder('Ej. +52 55 1234 5678')
                    ->tel()
                    ->minLength(10)
                    ->maxLength(12),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('role_title')->label('Puesto')->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('phone')->label('Teléfono')->searchable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Nuevo contacto'),
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
            ->emptyStateHeading('Sin contactos');
    }
}
