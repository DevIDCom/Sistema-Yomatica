<?php

namespace App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\Pages;

use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\CotizacionesResource;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\ProyectosResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCotizaciones extends EditRecord
{
    protected static string $resource = CotizacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crearProyecto')
                ->label('Crear Proyecto')
                ->icon('heroicon-o-briefcase')
                ->color('success')
                ->visible(fn () => $this->record->status === 'aprobada')
                ->url(fn () => ProyectosResource::getUrl('create', [
                    'cliente_id'     => $this->record->client_id,
                    'cotizacion_id'  => $this->record->id,
                ])),
            DeleteAction::make(),
        ];
    }
}
