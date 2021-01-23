<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesTable extends Migration {

	public function up()
	{
		Schema::create('roles', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('slug');
			$table->text('description');
			$table->string('type')->nullable();
			$table->string('group')->nullable();
			$table->timestamps();
			$table->boolean('status')->default(true);
		});
	}

	public function down()
	{
		Schema::drop('roles');
	}
}