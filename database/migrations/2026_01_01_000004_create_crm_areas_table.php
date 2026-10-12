<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('crm_branches')->onDelete('cascade');
            $table->string('name');
            $table->integer('floor_level')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('crm_areas');
    }
};