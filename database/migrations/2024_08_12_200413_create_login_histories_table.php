<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('login_histories', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
			$table->string('ip_address');
			$table->string('user_agent');
			$table->timestamp('login_at');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('login_histories');
	}
};
