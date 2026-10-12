<?php

namespace Database\Factories;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Cancelled;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Closed;
use App\Domains\Tickets\Domain\StateMachines\Ticket\InProgress;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Open;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Resolved;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class FsmTicketFactory extends Factory
{
    protected $model = FsmTicket::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'               => 'TCK-' . $this->faker->unique()->numberBetween(1000, 9999),
            'client_uuid'        => $this->faker->uuid(),
            'project_id'         => FsmProject::factory(),
            'area_id'            => CrmArea::factory(),
            'contact_id'         => CrmContact::factory(),
            'assigned_tech_id'   => User::factory(),
            'created_by'         => User::factory(),
            'title'              => $this->faker->sentence(),
            'description'        => $this->faker->paragraph(),
            'status'             => $this->faker->randomElement([Open::class, Closed::class, InProgress::class, Resolved::class, Cancelled::class]),
            'priority'           => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'is_geofence_valid'  => $this->faker->boolean(),
            'economic_weight'    => $this->faker->randomNumber(2, 200000),
            'scheduled_at'       => $this->faker->dateTime(),
            'due_at'             => $this->faker->dateTimeAD,
            'check_in_at'        => $this->faker->dateTimeAD,
            'check_out_at'       => $this->faker->dateTimeAD,
            'started_at'         => null,
            'started_by'         => null,
            'finished_at'        => null,
            'finished_by'        => null,
            'resolution'         => null,
        ];
    }
    public function usingExistingData(): static
    {
        return $this->state(fn (array $attributes) => [
            'Project_id' => FsmProject::first()?->id ?? FsmProject::factory(),
            'client_id' => CrmClient::first()?->id ?? CrmClient::factory(),
            'assigned_tech_id' => User::first()?->id ?? User::factory(),
            'area_id' => CrmArea::first()?->id?? CrmArea::factory(),
            'contact_id' => CrmContact::first()?->id?? CrmClient::factory(),
        ]);
    }
}
