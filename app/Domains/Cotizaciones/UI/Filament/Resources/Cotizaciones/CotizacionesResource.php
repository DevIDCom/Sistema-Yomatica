<?php

namespace App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones;

use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Pages\CreateCotizaciones;
use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Pages\EditCotizaciones;
use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Pages\ListCotizaciones;
use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Schemas\CotizacionesForm;
use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Tables\CotizacionesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CotizacionesResource extends Resource
{
    protected static ?string $model = CpqQuote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|null|\UnitEnum $navigationGroup = 'Administracion';
    protected static ?string $modelLabel = 'Cotizacion';
    protected static ?string $pluralModelLabel = 'Cotizaciones';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'CpqQuote';

    public static function form(Schema $schema): Schema
    {
        return CotizacionesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CotizacionesTable::configure($table);
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
            'index' => ListCotizaciones::route('/'),
            'create' => CreateCotizaciones::route('/create'),
            'edit' => EditCotizaciones::route('/{record}/edit'),
        ];
    }
}
