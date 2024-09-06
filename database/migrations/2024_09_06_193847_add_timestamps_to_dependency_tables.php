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

        Schema::table('locations', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('benefits', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns("locations", ["created_at", "updated_at"]);
        Schema::dropColumns("benefits", ["created_at", "updated_at"]);
        Schema::dropColumns("skills", ["created_at", "updated_at"]);
        Schema::dropColumns("categories", ["created_at", "updated_at"]);
    }
};
