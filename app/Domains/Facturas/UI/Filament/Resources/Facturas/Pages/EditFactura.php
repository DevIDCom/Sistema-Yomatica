<?php

namespace App\Domains\Facturas\UI\Filament\Resource\Facturas\Pages;

use App\Domains\Facturas\UI\Filament\Resource\Facturas\FacturaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFactura extends EditRecord
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
