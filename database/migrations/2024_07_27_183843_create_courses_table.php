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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('image')->default("https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTD3y8Eo9yo-VTrjq1pgCyR6PGkaTvNqrjrSZR4omN0UZeuu7jUjZXSKr4RMRteO_kTM3E&usqp=CAU");
            $table->boolean('published')->default(false);
            $table->boolean('paid')->default(false);
            $table->foreignUuid('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('category_id')->constrained('course_categories')->onDelete('cascade');
            $table->string('total_hours')->default(0);
            $table->integer('enrolls')->default(0);
            $table->integer('total_ratings')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
