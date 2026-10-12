<?php
namespace App\Domains\Proyectos\Domain\StateMachines\Project\Transitions;

use App\Domains\Proyectos\Domain\StateMachines\Project\Completed;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Closed;
use Spatie\ModelStates\Transition;

class ToCompleted extends Transition
{
    public function __construct(
        protected FsmProject $project
    )
    {
    }

    public function handle(): FsmProject
    {
        $this->project->status = new Completed($this->project);
        $this->project->financial_progress_percentage = 100;
        $this->project->save();

        foreach ($this->project->tickets as $ticket) {
            $currentTicketStatus = (string)$ticket->status;

            if (!in_array($currentTicketStatus, ['closed', 'cancelled'])) {
                if ($ticket->status->canTransitionTo(Closed::class)) {
                    $ticket->status->transitionTo(Closed::class);
                } else {
                    $ticket->status = new Closed($ticket);
                    $ticket->save();
                }
            }
        }

        return $this->project;
    }
}
