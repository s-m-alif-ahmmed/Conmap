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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('project_type_id')->nullable()->constrained('project_types')->nullOnDelete();
            $table->foreignId('duration_id')->nullable()->constrained('durations')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('client_name')->nullable();
            $table->string('local_authority')->nullable();
            $table->text('description')->nullable();
            $table->string('site_contact')->nullable();
            $table->string('site_reference')->nullable();
            $table->text('note')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('land_condition')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('project_build_type', ['Commercial', 'Residential'])->default('Commercial');
            $table->enum('land_status', ['Yes', 'No'])->default('No');
            $table->enum('visited_status', ['Yes', 'No'])->default('No');
            $table->enum('live_status', ['Live', 'Not Live'])->default('Live');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
