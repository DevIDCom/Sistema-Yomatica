<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Pages;

use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\ProyectosResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProyectos extends EditRecord
{
    protected static string $resource = ProyectosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
