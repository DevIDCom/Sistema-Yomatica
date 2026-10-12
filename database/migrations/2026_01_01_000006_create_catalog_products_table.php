<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('catalog_products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('unit_price', 15, 4);
            $table->longText('description');
            $
            $table->string('sat_product_key', 15);
            $table->string('sat_unit_key', 10);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('catalog_products');
    }
};
