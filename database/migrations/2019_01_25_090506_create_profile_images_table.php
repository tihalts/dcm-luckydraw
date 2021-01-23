<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileImagesTable extends Migration {

	public function up()
	{
		Schema::create('profile_images', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name', 255)->unique();
			$table->string('original_name', 255);
			$table->string('path', 255);
			$table->string('disk', 255);
			$table->string('extension', 255);
			$table->string('mime_type', 255);
			$table->string('size', 255);
			$table->string('model_type', 255);
			$table->bigInteger('model_id')->unsigned();
			$table->timestamps();
			$table->boolean('status')->default(true);
		});
	}

	public function down()
	{
		Schema::drop('profile_images');
	}
}