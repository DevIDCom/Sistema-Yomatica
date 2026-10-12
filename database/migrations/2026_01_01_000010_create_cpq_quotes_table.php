<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cpq_quotes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('cpq_quotes')->onDelete('set null');
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('restrict');
            $table->foreignId('branch_id')->nullable()->constrained('crm_branches')->onDelete('set null');
            $table->string('status', 30);
            $table->string('currency', 3)->default('MXN');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cpq_quotes');
    }
};