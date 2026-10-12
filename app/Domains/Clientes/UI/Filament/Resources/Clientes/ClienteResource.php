<?php

namespace App\Domains\Clientes\UI\Filament\Resources\Clientes;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;

use App\Domains\Clientes\UI\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\Pages\EditCliente;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\Pages\ListClientes;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\RelationManagers\ContactsRelationManager;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\RelationManagers\SucursalesRelationManager;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\Schemas\ClienteForm;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\Tables\ClientesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;


class ClienteResource extends Resource
{
    protected static ?string $model = CrmClient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;
    protected static ?string $modelLabel = 'Clientes';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?string $recordTitleAttribute = 'facturapi_id';
    protected static string|null|\UnitEnum $navigationGroup = 'Comercio';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ClienteForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return ClientesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SucursalesRelationManager::class,
            ContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }
}
