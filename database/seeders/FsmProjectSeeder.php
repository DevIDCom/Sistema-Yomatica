<?php

namespace Database\Seeders;

use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FsmProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FsmProject::factory()->count(55)->create();
    }
}
