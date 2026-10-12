<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('billing_invoices')->onDelete('cascade');
            $table->string('sat_product_key', 15);
            $table->string('sat_unit_key', 10);
            $table->text('description');
            $table->decimal('total', 15, 2);
        });
    }

    public function down(): void {
        Schema::dropIfExists('billing_invoice_items');
    }
};