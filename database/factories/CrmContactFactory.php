<?php

namespace Database\Factories;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class CrmContactFactory extends Factory
{
    protected $model = CrmContact::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id'  => CrmClient::factory(),
            'branch_id'  => CrmBranch::factory(),
            'name'       => $this->faker->name(),
            'phone'      => $this->faker->phoneNumber(),
            'email'      => $this->faker->unique()->safeEmail(),
            'role_title' => $this->faker->jobTitle(),
        ];
    }
    public function usingExistingData(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => CrmClient::first()?->id ?? CrmClient::factory(),
            'branch_id' => CrmBranch::first()?->id ?? CrmBranch::factory(),

        ]);
    }
}
