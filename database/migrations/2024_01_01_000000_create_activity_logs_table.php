<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
	public function up()
	{
		Schema::connection(config('activitylog.connection'))->create('activity_logs', function (Blueprint $table) {
			$table->id();
			$table->string('log_name')->nullable();
			$table->string('log_level')->default('info');
			$table->text('description');
			$table->nullableMorphs('subject');
			$table->nullableMorphs('causer');
			$table->json('properties')->nullable();
			$table->timestamps();

			// Indexes for performance
			$table->index('log_name');
			$table->index('log_level');
		});
	}

	public function down()
	{
		Schema::connection(config('activitylog.connection'))->dropIfExists('activity_logs');
	}
}
