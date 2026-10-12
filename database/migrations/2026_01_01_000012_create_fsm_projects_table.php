<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->nullable()->unique()->constrained('cpq_quotes')->onDelete('restrict');
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('restrict');
            $table->string('code', 50)->unique();
            $table->string('status', 30);
            $table->decimal('total_budget', 15, 2);
            $table->decimal('financial_progress_percentage', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('fsm_projects');
    }
};