<?php

namespace App\Domains\Facturas\UI\Filament\Resource\Facturas\Pages;

use App\Domains\Facturas\UI\Filament\Resource\Facturas\FacturaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFacturas extends ListRecords
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
