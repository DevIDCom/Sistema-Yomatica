<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_uuid');
            $table->foreignId('ticket_id')->nullable()->constrained('fsm_tickets')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('billing_invoices')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('crm_clients')->onDelete('cascade');
            $table->string('file_path', 500);
            $table->text('s3_key');
            $table->string('file_type', 50);
            $table->geometry('location', subtype: 'point', srid: 4326)->nullable();
            $table->boolean('is_out_of_bounds')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('ALTER TABLE evidences ADD CONSTRAINT chk_single_evidenciable CHECK (num_nonnulls(ticket_id, project_id, invoice_id, client_id) = 1);');
        DB::statement('CREATE INDEX idx_evidences_location ON evidences USING GIST (location);');
        DB::statement('CREATE INDEX idx_evidences_ticket_id ON evidences (ticket_id) WHERE ticket_id IS NOT NULL;');
        DB::statement('CREATE INDEX idx_evidences_project_id ON evidences (project_id) WHERE project_id IS NOT NULL;');
    }

    public function down(): void {
        Schema::dropIfExists('evidences');
    }
};