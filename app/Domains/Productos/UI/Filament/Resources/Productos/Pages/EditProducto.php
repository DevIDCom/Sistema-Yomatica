<?php

namespace App\Domains\Productos\UI\Filament\Resources\Productos\Pages;

use App\Domains\Productos\Dominian\Services\FacturapiProductoService;
use App\Domains\Productos\UI\Filament\Resources\Productos\ProductoResource;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

//    protected function afterSave(): void
//    {
//        $producto = $this->record;
//        $servicio = app(FacturapiProductoService::class);
//
//        try {
//            if ($producto->facturapi_id) {
//                $servicio->actualizar($producto);
//            } else {
//                $servicio->crear($producto);
//            }
//
//            Notification::make()
//                ->title('Producto actualizado en Facturapi')
//                ->success()
//                ->send();
//
//        } catch (Exception $e) {
//            Notification::make()
//                ->title('Error de sincronización con Facturapi')
//                ->body($e->getMessage())
//                ->warning()
//                ->persistent()
//                ->send();
//        }
//    }
}
