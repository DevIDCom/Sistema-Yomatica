<?php

namespace Database\Factories;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class CrmClientFactory extends Factory
{
    protected $model = CrmClient::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $faker = $this->faker;
        return [
            'uuid' => $faker->uuid(),
            'business_name'=> $faker->company(),
            'tax_id' => $faker->ein(),
            'tax_regime'=> $faker->ean13(),
            'zip_code' => $faker->postcode(),
            'type' => $faker->mimeType(),
            'email'=>$faker->email(),
            'phone'=>$faker->phoneNumber(),
            'cfdi_use' => $faker->randomElement(['G01', 'G03', 'P01', 'CP01']),
        ];
    }
}
