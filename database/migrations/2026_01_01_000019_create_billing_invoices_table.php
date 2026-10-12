<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('restrict');
            $table->foreignId('milestone_id')->nullable()->unique()->constrained('project_billing_milestones')->onDelete('restrict');
            $table->foreignId('quote_id')->nullable()->constrained('cpq_quotes')->onDelete('restrict');
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('restrict');
            $table->string('facturapi_id', 100)->nullable();
            $table->uuid('fiscal_uuid')->nullable()->unique();
            $table->string('status', 30);
            $table->string('cfdi_use', 10);
            $table->string('tax_regime', 10);
            $table->string('receiver_zip_code', 10);
            $table->decimal('total', 15, 2);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('billing_invoices');
    }
};