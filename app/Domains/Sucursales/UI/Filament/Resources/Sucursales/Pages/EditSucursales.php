<?php

namespace App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Pages;

use App\Domains\Sucursales\UI\Filament\Resources\Sucursales\SucursalesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSucursales extends EditRecord
{
    protected static string $resource = SucursalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
