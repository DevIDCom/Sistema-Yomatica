<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_billing_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('fsm_projects')->onDelete('cascade');
            $table->string('name', 150);
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 15, 2);
            $table->string('status', 30);
            $table->date('due_date');
        });
    }

    public function down(): void {
        Schema::dropIfExists('project_billing_milestones');
    }
};