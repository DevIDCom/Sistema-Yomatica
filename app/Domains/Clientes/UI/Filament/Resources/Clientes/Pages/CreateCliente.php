<?php

namespace App\Domains\Clientes\UI\Filament\Resources\Clientes\Pages;

use App\Domains\Clientes\Domain\Services\FacturapiClienteService;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\ClienteResource;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;

//    protected function afterCreate(): void
//    {
//        $cliente = $this->record;
//        $servicio = app(FacturapiClienteService::class);
//
//        try {
//            $servicio->crear($cliente);
//
//            Notification::make()
//                ->title('Cliente registrado en BD y en Facturapi')
//                ->success()
//                ->send();
//
//        } catch (Exception $e) {
//            Notification::make()
//                ->title('Cliente guardado localmente, pero falló en Facturapi')
//                ->body($e->getMessage())
//                ->warning()
//                ->persistent()
//                ->send();
//        }
//    }
}
