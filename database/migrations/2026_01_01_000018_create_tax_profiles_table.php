<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tax_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('tax_id', 15)->unique();
            $table->string('legal_name');
            $table->string('tax_regime', 10);
            $table->string('zip_code', 10);
            $table->binary('facturapi_api_key_encrypted');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tax_profiles');
    }
};