<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolePermissionsTable extends Migration {

	public function up()
	{
		Schema::create('role_permissions', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('permission_id');
			$table->unsignedBigInteger('role_id');
			$table->timestamps();
			$table->boolean('status')->default(true);
		});
	}

	public function down()
	{
		Schema::drop('role_permissions');
	}
}