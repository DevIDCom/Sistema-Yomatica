<?php

namespace App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Pages;

use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\SucursalesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSucursales extends ListRecords
{
    protected static string $resource = SucursalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
