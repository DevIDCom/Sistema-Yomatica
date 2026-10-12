<?php

namespace Database\Factories;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Proyectos\Domain\StateMachines\Project\Active;
use App\Domains\Proyectos\Domain\StateMachines\Project\Cancelled;
use App\Domains\Proyectos\Domain\StateMachines\Project\Completed;
use App\Domains\Proyectos\Domain\StateMachines\Project\Draft;
use App\Domains\Proyectos\Domain\StateMachines\Project\OnHold;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class FsmProjectFactory extends Factory
{
    protected $model = FsmProject::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quote_id'=>CpqQuote::factory(),
            'client_id'=>CrmClient::factory(),
            'code'=>$this->faker->unique()->ean8(),
            'name'=>$this->faker->name(),
            'status'=>$this->faker->randomElement([Active::class,Cancelled::class, Completed::class, Draft::class, OnHold::class]),
            'total_budget'=>$this->faker->randomNumber(2, 10),
            'financial_progress_percentage'=>$this->faker->randomNumber(1, 100),
        ];
    }
    public function usingExistingData(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_id' => CpqQuote::first()?->id ?? CpqQuote::factory(),
            'client_id' => CrmClient::first()?->id ?? CrmClient::factory(),
        ]);
    }
}
