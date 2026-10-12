<?php

namespace Database\Factories;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Cotizaciones\Domain\StateMachines\Quote\Approved;
use App\Domains\Cotizaciones\Domain\StateMachines\Quote\Draft;
use App\Domains\Cotizaciones\Domain\StateMachines\Quote\Expired;
use App\Domains\Cotizaciones\Domain\StateMachines\Quote\Rejected;
use App\Domains\Cotizaciones\Domain\StateMachines\Quote\Sent;
use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class CpqQuoteFactory extends Factory
{
    protected $model = CpqQuote::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'parent_id' => null,
            'client_id' => CrmClient::factory(),
            'branch_id' => CrmBranch::factory(),
            'status' => $this->faker->randomElement([Draft::class, Sent::class, Expired::class, Rejected::class, Approved::class]),
            'currency' => $this->faker->currencyCode(),
            'exchange_rate' => $this->faker->randomNumber(4),
            'total' => $this->faker->randomFloat(),
        ];
    }
    public function usingExistingData(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => CrmClient::inRandomOrder()->first()?->id ?? CrmClient::factory(),
            'branch_id' => CrmBranch::inRandomOrder()->first()?->id ?? CrmBranch::factory(),
        ]);
    }

}
