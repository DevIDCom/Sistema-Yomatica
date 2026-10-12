<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->string('name');
            $table->text('address');
            $table->geometry('location', subtype: 'point', srid: 4326)->nullable();
            $table->timestamps();
        });

        DB::statement('CREATE INDEX idx_crm_branches_location ON crm_branches USING GIST (location);');
    }

    public function down(): void {
        Schema::dropIfExists('crm_branches');
    }
};