<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_ticket_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('fsm_tickets')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('catalog_products')->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('restrict');
            $table->decimal('quantity_used', 12, 4);
            $table->decimal('unit_cost', 15, 4);
            $table->timestampTz('client_updated_at');
            $table->timestampTz('server_updated_at')->useCurrent();

            $table->unique(['ticket_id', 'product_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('fsm_ticket_materials');
    }
};