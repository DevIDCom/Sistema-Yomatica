<?php

namespace App\Domains\Proyectos\Domain\StateMachines\Project;

class OnHold extends ProjectState
{
    public static string $name = 'on_hold';

    public function label(): string { return 'En Pausa'; }
    public function color(): string { return 'warning'; }
    public function icon(): string { return 'heroicon-o-pause'; }
}
