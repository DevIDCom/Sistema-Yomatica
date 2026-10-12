<?php

namespace Database\Seeders;


use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CrmClientSeeder::class,
            CrmBranchSeeder::class,
            CpqQuoteSeeder::class,
            CrmAreaSeeder::class,
            FsmProjectSeeder::class,
            CrmContactSeeder::class,
            FsmTicketSeeder::class,
        ]);
    }
}
