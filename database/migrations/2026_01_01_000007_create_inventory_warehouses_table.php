<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('assigned_tech_id')->nullable()->unique()->constrained('users')->onDelete('set null');
            $table->enum('type', ['CENTRAL', 'VAN', 'VIRTUAL']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_warehouses');
    }
};