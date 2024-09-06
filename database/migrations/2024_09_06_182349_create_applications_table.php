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

        schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('type');
            $table->foreignId('candidate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('job_listings')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('form_application', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->timestamps();
        });

        schema::create('cv_application', function (Blueprint $table) {
            $table->id();
            $table->string('cv');
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_application');
        Schema::dropIfExists('form_application');
        Schema::dropIfExists('applications');
    }
};
