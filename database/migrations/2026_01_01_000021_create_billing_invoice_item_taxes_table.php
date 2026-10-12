<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_invoice_item_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_item_id')->constrained('billing_invoice_items')->onDelete('cascade');
            $table->enum('tax_type', ['IVA', 'ISR', 'IEPS']);
            $table->enum('factor_type', ['Tasa', 'Cuota', 'Exento']);
            $table->decimal('rate_or_fee', 6, 4);
            $table->decimal('amount', 15, 2);
        });
    }

    public function down(): void {
        Schema::dropIfExists('billing_invoice_item_taxes');
    }
};