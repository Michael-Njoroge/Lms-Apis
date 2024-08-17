<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwoFactorAuthenticationsTable extends Migration {
	public function up() {
		Schema::create('two_factor_authentications', function (Blueprint $table) {
			$table->id();
			$table->text('authenticatable_id');
			$table->string('authenticatable_type');
			$table->text('two_factor_secret')->nullable();
			$table->json('recovery_codes')->nullable();
			$table->integer('digits')->default(6);
			$table->integer('seconds')->default(30);
			$table->integer('window')->default(1);
			$table->string('algorithm')->default('sha1');
			$table->timestamp('recovery_codes_generated_at')->nullable();
			$table->json('safe_devices')->nullable();
			$table->timestamp('enabled_at')->nullable();
			$table->text('shared_secret')->nullable();
			$table->string('label')->nullable();
			$table->timestamps();
		});
	}

	public function down() {
		Schema::dropIfExists('two_factor_authentications');
	}
}
