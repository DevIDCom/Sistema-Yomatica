<?php

namespace App\Domains\Productos\UI\Filament\Resources\Productos;

use App\Domains\Productos\Infraestructure\Eloquent\Models\BillingInvoiceItem;
use App\Domains\Productos\UI\Filament\Resources\Productos\Pages\CreateProducto;
use App\Domains\Productos\UI\Filament\Resources\Productos\Pages\EditProducto;
use App\Domains\Productos\UI\Filament\Resources\Productos\Pages\ListProductos;
use App\Domains\Productos\UI\Filament\Resources\Productos\Schemas\ProductoForm;
use App\Domains\Productos\UI\Filament\Resources\Productos\Tables\ProductosTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductoResource extends Resource
{
    protected static ?string $model = BillingInvoiceItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected static string|null|\UnitEnum $navigationGroup = 'Comercio';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Productos';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $recordTitleAttribute = 'facturapi_id';

    public static function form(Schema $schema): Schema
    {
        return ProductoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductos::route('/'),
            'create' => CreateProducto::route('/create'),
            'edit' => EditProducto::route('/{record}/edit'),
        ];
    }
}
