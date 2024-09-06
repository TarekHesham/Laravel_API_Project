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
        // Job Tables
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->string('description');
            $table->string('experience_level');
            $table->integer('number_of_applications')->default(0);
            $table->integer('salary_from');
            $table->integer('salary_to');
            $table->enum('status', ['pending', 'open', 'closed'])->default('pending');
            $table->enum('work_type', ['remote', 'onsite', 'hybrid']);
            $table->dateTime('deadline');
            $table->foreignId('location_id')->constraind('locations')->onDelete('set null');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('job_benefits', function (Blueprint $table) {
            $table->foreignId('job_listing_id')->constrained('job_listings')->onDelete('cascade');
            $table->foreignId('benefit_id')->constrained('benefits')->onDelete('cascade');
            $table->unique(['job_listing_id', 'benefit_id']);
        });

        Schema::create('job_skills', function (Blueprint $table) {
            $table->foreignId('job_listing_id')->constrained('job_listings')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->unique(['job_listing_id', 'skill_id']);
        });

        Schema::create('job_category', function (Blueprint $table) {
            $table->foreignId('job_listing_id')->constrained('job_listings')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->unique(['job_listing_id', 'category_id']);
        });

        Schema::create('job_images', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->foreignId('job_listing_id')->constrained('job_listings')->onDelete('cascade');
            $table->unique(['job_listing_id', 'image']);
        });

        schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('job_listings')->onDelete('cascade');
            $table->timestamps();
        });

        // Employer Tables
        Schema::create('employer_jobs', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_listing_id')->constrained('job_listings')->onDelete('cascade');
            $table->timestamps();
        });

        // Candidate Tables
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
        Schema::dropIfExists('employer_jobs');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('job_images');
        Schema::dropIfExists('job_category');
        Schema::dropIfExists('job_skills');
        Schema::dropIfExists('job_benefits');
        Schema::dropIfExists('job_listings');
    }
};
