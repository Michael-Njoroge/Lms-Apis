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
        Schema::create('blogs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('thumbnail')->default("https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTD3y8Eo9yo-VTrjq1pgCyR6PGkaTvNqrjrSZR4omN0UZeuu7jUjZXSKr4RMRteO_kTM3E&usqp=CAU");
            $table->string('description');
            $table->json('keywords');
            $table->timestamps();
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignUuid('category')->constrained('blog_categories')->onDelete('cascade');
        });

        Schema::create('document_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('documentations', function (Blueprint $table) {
            $table->foreignUuid('category')->constrained('document_categories')->onDelete('cascade');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('document_categories');
    }
};
