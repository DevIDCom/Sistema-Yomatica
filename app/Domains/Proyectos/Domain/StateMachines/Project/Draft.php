<?php

namespace App\Domains\Proyectos\Domain\StateMachines\Project;

class Draft extends ProjectState
{
    public static string $name = 'draft';
    public function label(): string { return 'Borrador'; }
    public function color(): string { return 'gray'; }
    public function icon(): string { return 'heroicon-o-document-text'; }
}
