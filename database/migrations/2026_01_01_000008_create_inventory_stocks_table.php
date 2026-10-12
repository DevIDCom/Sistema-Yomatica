<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('catalog_products')->onDelete('restrict');
            $table->decimal('stock_quantity', 12, 4)->default(0);
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['warehouse_id', 'product_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_stocks');
    }
};