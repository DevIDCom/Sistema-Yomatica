<?php

namespace Database\Factories;

use App\Domains\Productos\Infraestructure\Eloquent\Models\BillingInvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class BillingInvoiceItemFactory extends Factory
{
    protected $model = BillingInvoiceItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id'=>null,
            'sat_product_key'=>$this->faker->randomNumber(),
            'sat_unit_key'=>$this->faker->randomNumber(),
            'description'=>$this->faker->text(),
            'total'=>$this->faker->numberBetween(1,100),
            'file_path',
            'file_type'
        ];
    }
}
