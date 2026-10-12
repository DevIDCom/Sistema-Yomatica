<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('crm_branches')->onDelete('set null');
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('role_title', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('crm_contacts');
    }
};