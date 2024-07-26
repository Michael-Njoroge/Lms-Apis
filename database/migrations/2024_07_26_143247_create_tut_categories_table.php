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
        Schema::create('tut_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('image')->default('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTD3y8Eo9yo-VTrjq1pgCyR6PGkaTvNqrjrSZR4omN0UZeuu7jUjZXSKr4RMRteO_kTM3E&usqp=CAU');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tut_categories');
    }
};
