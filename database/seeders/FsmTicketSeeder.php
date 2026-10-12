<?php

namespace Database\Seeders;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FsmTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FsmTicket::factory()->count(5)->create();
    }
}
