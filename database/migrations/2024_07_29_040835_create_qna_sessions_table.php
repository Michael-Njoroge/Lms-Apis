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
        Schema::create('qna_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });

        Schema::create('questions', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('vote_count')->default(0);
            $table->timestamps();
        });

        Schema::create('answers', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('description');
            $table->integer('vote_count')->default(0);
            $table->timestamps();
        });

        Schema::create('qna_votes', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('question_id')->nullable()->constrained('questions')->onDelete('set null');
            $table->foreignUuid('answer_id')->nullable()->constrained('answers')->onDelete('set null');
            $table->enum('vote_type', ['up_vote', 'down_vote']);
            $table->timestamps();
        });

        Schema::table('qna_sessions', function(Blueprint $table) {
            $table->foreignUuid('question_id')->nullable()->constrained('questions')->onDelete('cascade');
            $table->foreignUuid('answer_id')->nullable()->constrained('answers')->onDelete('cascade');
        });

        Schema::create('qna_tags', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('total_questions')->default(0);
            $table->timestamps();
        });

        Schema::create('qna_comments', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment');
            $table->foreignUuid('question_id')->nullable()->constrained('questions')->onDelete('set null');
            $table->foreignUuid('answer_id')->nullable()->constrained('answers')->onDelete('set null');
            $table->timestamps();
        });

        Schema::table('questions', function(Blueprint $table) {
            $table->foreignUuid('tag_id')->nullable()->constrained('qna_tags')->onDelete('cascade');
        });

        Schema::table('answers', function(Blueprint $table) {
            $table->foreignUuid('question_id')->constrained('questions')->onDelete('cascade')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qna_sessions'); 
        Schema::dropIfExists('qna_votes'); 
        Schema::dropIfExists('questions');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('qna_tags');
        Schema::dropIfExists('qna_comments');
    }
};
