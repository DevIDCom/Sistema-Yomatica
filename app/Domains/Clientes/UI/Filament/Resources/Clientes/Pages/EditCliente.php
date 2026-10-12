<?php

namespace App\Domains\Clientes\UI\Filament\Resources\Clientes\Pages;

use App\Domains\Clientes\Domain\Services\FacturapiClienteService;
use App\Domains\Clientes\UI\Filament\Resources\Clientes\ClienteResource;
use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\CotizacionesResource;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
           Action::make('crearCotizacion')
                ->label('Crear Cotización')
                ->icon('heroicon-o-document-plus')
                ->url(fn () => CotizacionesResource::getUrl('create', ['cliente_id' => $this->record->id])),

            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $cliente = $this->record;
        $servicio = app(FacturapiClienteService::class);

        try {
            if ($cliente->facturapi_id) {
                $servicio->actualizar($cliente);
            } else {
                $servicio->crear($cliente);
            }

            Notification::make()
                ->title('Cliente actualizado en Facturapi')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Error de sincronización con Facturapi')
                ->body($e->getMessage())
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
