<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->nullable()->constrained('cpq_quotes')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('cascade');
            $table->foreignId('ticket_id')->nullable()->constrained('fsm_tickets')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('billing_invoices')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('from_status', 50);
            $table->string('to_status', 50);
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('ALTER TABLE status_histories ADD CONSTRAINT chk_single_statusable CHECK (num_nonnulls(quote_id, project_id, ticket_id, invoice_id) = 1);');
        DB::statement('CREATE INDEX idx_status_histories_ticket_id ON status_histories (ticket_id) WHERE ticket_id IS NOT NULL;');
    }

    public function down(): void {
        Schema::dropIfExists('status_histories');
    }
};