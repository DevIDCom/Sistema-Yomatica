<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cpq_quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('cpq_quotes')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('catalog_products')->onDelete('restrict');
            $table->text('description');
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('unit_cost', 15, 4);
        });
    }

    public function down(): void {
        Schema::dropIfExists('cpq_quote_items');
    }
};