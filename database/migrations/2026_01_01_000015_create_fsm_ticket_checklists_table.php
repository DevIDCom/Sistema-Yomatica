<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_ticket_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('fsm_tickets')->onDelete('cascade');
            $table->jsonb('data')->default('{}');
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('client_updated_at');
            $table->timestampTz('server_updated_at')->useCurrent();
        });

        DB::statement('CREATE INDEX idx_checklists_data_gin ON fsm_ticket_checklists USING GIN (data);');
    }

    public function down(): void {
        Schema::dropIfExists('fsm_ticket_checklists');
    }
};