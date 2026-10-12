<?php

namespace App\Domains\Proyectos\Domain\StateMachines\Project;

class Cancelled extends ProjectState
{
    public static string $name = 'cancelled';

    public function label(): string { return 'Cancelado'; }
    public function color(): string { return 'danger'; }
    public function icon(): string { return 'heroicon-o-x-circle'; }
}
