<?php

namespace Database\Seeders;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use Illuminate\Database\Seeder;

class CrmClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CrmClient::factory()->count(12)->create();
    }
}
