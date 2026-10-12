<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crm_clients', function (Blueprint $table) {
            $table->string('email')->after('zip_code');
            $table->string('phone', 20)->after('email');
            $table->string('cfdi_use', 10)->default('G03')->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_clients', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'cfdi_use']);
        });
    }
};
