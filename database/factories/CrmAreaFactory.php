<?php

namespace Database\Factories;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class CrmAreaFactory extends Factory
{
    protected $model = CrmArea::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => CrmBranch::factory(),
            'name' => $this->faker->word(),
            'floor_level' => $this->faker->numberBetween(1, 7),
        ];
    }
    public function usingExistingData(): static
    {
        return $this->state(fn (array $attributes) => [
            'branch_id' => CrmBranch::inRandomOrder()->first()?->id ?? CrmBranch::factory(),
        ]);
    }
}
