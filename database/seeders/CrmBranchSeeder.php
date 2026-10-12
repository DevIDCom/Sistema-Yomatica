<?php

namespace Database\Seeders;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Illuminate\Database\Seeder;

class CrmBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CrmBranch::factory()->count(20)->create();

    }
}
