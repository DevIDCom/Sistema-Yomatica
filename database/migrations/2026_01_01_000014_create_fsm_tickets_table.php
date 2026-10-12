<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->nullable()->unique();

            $table->uuid('client_uuid');
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained('crm_areas')->onDelete('set null');
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->onDelete('set null');
            $table->foreignId('assigned_tech_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('status', 30);
            $table->string('priority', 20)->default('medium');
            $table->integer('lock_version')->default(1);

            $table->geometry('check_in_location', subtype: 'point', srid: 4326)->nullable();
            $table->geometry('check_out_location', subtype: 'point', srid: 4326)->nullable();
            $table->boolean('is_geofence_valid')->default(false);
            $table->decimal('economic_weight', 10, 2)->default(0.00);

            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('due_at')->nullable();

            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->foreignId('started_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('finished_at')->nullable();
            $table->foreignId('finished_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id', 'idx_fsm_tickets_project');
            $table->index('client_uuid', 'idx_fsm_tickets_client');
            $table->index('area_id', 'idx_fsm_tickets_area');
            $table->index('contact_id', 'idx_fsm_tickets_contact');
            $table->index('assigned_tech_id', 'idx_fsm_tickets_tech');
            $table->index('created_by', 'idx_fsm_tickets_creator');
            $table->index('started_by', 'idx_fsm_tickets_starter');
            $table->index('finished_by', 'idx_fsm_tickets_finisher');

            $table->index(['status', 'assigned_tech_id'], 'idx_fsm_tickets_status_tech');

            $table->index('scheduled_at', 'idx_fsm_tickets_scheduled');
            $table->index('due_at', 'idx_fsm_tickets_due');
        });

        DB::statement('CREATE INDEX idx_fsm_tickets_checkin ON fsm_tickets USING GIST (check_in_location);');
    }

    public function down(): void {
        Schema::dropIfExists('fsm_tickets');
    }
};
