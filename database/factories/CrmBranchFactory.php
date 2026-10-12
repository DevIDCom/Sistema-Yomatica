<?php

namespace Database\Factories;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class CrmBranchFactory extends Factory
{
    protected $model = CrmBranch::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id'=>CrmClient::factory(),
            'name'=>$this->faker->company(),
            'address'=>$this->faker->address(),
        ];
    }
}
