<?php

namespace Database\Seeders;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrmContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CrmContact::factory()->count(30)->create();
    }
}
