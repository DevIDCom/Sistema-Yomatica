<?php

namespace Database\Seeders;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;
use Illuminate\Database\Seeder;

class CrmAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CrmArea::factory()->count(40)->create();
    }
}
