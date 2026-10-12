<?php

namespace App\Domains\Proyectos\Domain\StateMachines\Project;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ProjectState extends State
{
    abstract public function label(): string;
    abstract public function color(): string;
    abstract public function icon(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Active::class)
            ->allowTransition(Draft::class, Cancelled::class)
            ->allowTransition(Active::class, OnHold::class)
            ->allowTransition(Active::class, Completed::class)
            ->allowTransition(Active::class, Cancelled::class)
            ->allowTransition(OnHold::class, Active::class)
            ->allowTransition(OnHold::class, Cancelled::class)
            ->allowTransition(Completed::class, Active::class)
            ->allowTransition(Cancelled::class, Draft::class);
    }

    public static function getSelectOptions(): array
    {
        return [
            Draft::$name     => 'Borrador',
            Active::$name    => 'Activo',
            OnHold::$name    => 'En Pausa',
            Completed::$name => 'Completado',
            Cancelled::$name => 'Cancelado',
        ];
    }
}
