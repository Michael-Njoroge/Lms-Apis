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
        Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('star')->default(0);
            $table->text('comment');
            $table->foreignUuid('posted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('course_rating', function (Blueprint $table) {
            $table->foreignUuid('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignUuid('rating_id')->constrained('ratings')->onDelete('cascade');
            $table->primary(['course_id', 'rating_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
