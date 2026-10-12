<?php

namespace App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Pages;

use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\CotizacionesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCotizaciones extends ListRecords
{
    protected static string $resource = CotizacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
