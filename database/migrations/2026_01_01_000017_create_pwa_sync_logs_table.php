<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('pwa_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('batch_uuid');
            $table->jsonb('payload');
            $table->string('status', 30);
            $table->boolean('has_conflict')->default(false);
            $table->jsonb('conflict_details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('CREATE INDEX idx_pwa_payload_gin ON pwa_sync_logs USING GIN (payload);');
        DB::statement('CREATE INDEX idx_pwa_conflict_details_gin ON pwa_sync_logs USING GIN (conflict_details);');
    }

    public function down(): void {
        Schema::dropIfExists('pwa_sync_logs');
    }
};