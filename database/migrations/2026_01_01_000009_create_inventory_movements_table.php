<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('catalog_products')->onDelete('restrict');
            $table->enum('movement_type', ['INBOUND', 'OUTBOUND_TICKET', 'TRANSFER', 'ADJUSTMENT']);
            $table->decimal('quantity', 12, 4);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_movements');
    }
};