<?php

namespace App\Domains\Productos\UI\Filament\Resources\Productos\Pages;

use App\Domains\Productos\UI\Filament\Resources\Productos\ProductoResource;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

//    protected function afterCreate(): void
//    {
//        $producto = $this->record;
//        $servicio = app(FacturapiProductoService::class);
//
//        try {
//            $servicio->crear($producto);
//
//            Notification::make()
//                ->title('Producto sincronizado con Facturapi')
//                ->success()
//                ->send();
//
//        } catch (Exception $e) {
//            Notification::make()
//                ->title('Fallo en Facturapi')
//                ->body($e->getMessage())
//                ->warning()
//                ->persistent()
//                ->send();
//        }
//    }
}
