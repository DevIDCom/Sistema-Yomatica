<?php

namespace App\Domains\Facturas\UI\Filament\Resource\Facturas;

use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;
use App\Domains\Facturas\UI\Filament\Resource\Facturas\Pages\CreateFactura;
use App\Domains\Facturas\UI\Filament\Resource\Facturas\Pages\EditFactura;
use App\Domains\Facturas\UI\Filament\Resource\Facturas\Pages\ListFacturas;
use App\Domains\Facturas\UI\Filament\Resource\Facturas\Schemas\FacturaForm;
use App\Domains\Facturas\UI\Filament\Resource\Facturas\Tables\FacturasTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FacturaResource extends Resource
{
    protected static ?string $model = BillingInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;
    protected static string|null|\UnitEnum $navigationGroup = 'Comercio';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Facturas';

    public static function form(Schema $schema): Schema
    {
        return FacturaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacturasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFacturas::route('/'),
            'create' => CreateFactura::route('/create'),
            'edit' => EditFactura::route('/{record}/edit'),
        ];
    }
}
