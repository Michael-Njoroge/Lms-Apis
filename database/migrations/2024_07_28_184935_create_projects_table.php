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
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();;
            $table->json('links')->nullable();
            $table->string('author')->default('Michael Njoroge');
            $table->json('images')->nullable();
            $table->foreignUuid('category_id')->constrained('project_categories')->onDelete('cascade');
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('price_after_discount', 8, 2)->default(0);
            $table->boolean('published')->default(false);
            $table->json('tech_stack')->nullable();
            $table->json('keywords')->nullable();
            $table->timestamps();
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
