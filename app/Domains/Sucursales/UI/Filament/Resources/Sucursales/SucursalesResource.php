<?php

namespace App\Domains\Sucursales\UI\Filament\Resources\Sucursales;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Pages\CreateSucursales;
use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Pages\EditSucursales;
use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Pages\ListSucursales;
use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\RelationManagers\AreasRelationManager;
use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Schemas\SucursalesForm;
use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Tables\SucursalesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SucursalesResource extends Resource
{
    protected static ?string $model = CrmBranch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CrmBranch';
    protected static ?string $modelLabel = 'Sucursal';
    protected static ?string $pluralModelLabel = 'Sucursales';
    protected static string|null|\UnitEnum $navigationGroup = 'Comercio';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SucursalesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SucursalesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AreasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSucursales::route('/'),
            'create' => CreateSucursales::route('/create'),
            'edit' => EditSucursales::route('/{record}/edit'),
        ];
    }
}
