<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Pages;

use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\ProyectosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProyectos extends ListRecords
{
    protected static string $resource = ProyectosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
