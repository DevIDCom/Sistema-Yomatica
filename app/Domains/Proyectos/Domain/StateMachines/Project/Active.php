<?php

namespace App\Domains\Proyectos\Domain\StateMachines\Project;

class Active extends ProjectState
{
    public static string $name = 'active';

    public function label(): string { return 'Activo'; }
    public function color(): string { return 'info'; }
    public function icon(): string { return 'heroicon-o-play'; }
}
