<?php

namespace App\Domains\Proyectos\Domain\StateMachines\Project;

class Completed extends ProjectState
{
    public static string $name = 'completed';

    public function label(): string { return 'Completado'; }
    public function color(): string { return 'success'; }
    public function icon(): string { return 'heroicon-o-check-circle'; }
}
