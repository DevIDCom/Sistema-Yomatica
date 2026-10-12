<?php

namespace App\Domains\Facturas\UI\Filament\Resource\Facturas\Pages;

use App\Domains\Facturas\Dominian\Services\FacturapiFacturaService;
use App\Domains\Facturas\UI\Filament\Resource\Facturas\FacturaResource;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateFactura extends CreateRecord
{
    protected static string $resource = FacturaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $conceptos = $data['conceptos'] ?? [];

        $factura = static::getModel()::create($data);

        try {
            $servicio = app(FacturapiFacturaService::class);
            $servicio->timbrar($factura, $conceptos);

            Notification::make()
                ->title('¡Factura Timbrada con Éxito!')
                ->success()
                ->send();

        } catch (Exception $e) {
            $factura->delete();

            Notification::make()
                ->title('Error al timbrar')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        return $factura;
    }
}
