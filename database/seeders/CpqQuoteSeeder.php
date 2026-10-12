<?php

namespace Database\Seeders;

use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use Illuminate\Database\Seeder;

class CpqQuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CpqQuote::factory()->count(12)->create();

    }
}
