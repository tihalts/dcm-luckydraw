<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionsTable extends Migration {

	public function up()
	{
		Schema::create('permissions', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('slug');
			$table->string('description');
			$table->string('type')->nullable();
			$table->string('group');
			$table->timestamps();
			$table->boolean('status')->default(true);
		});
	}

	public function down()
	{
		Schema::drop('permissions');
	}
}