<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('address1')->nullable();
			$table->string('address2')->nullable();
            $table->string('address_type')->default('office');
            $table->string('post_code')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->string('model_type');
			$table->unsignedBigInteger('model_id');
			$table->timestamps();
			$table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
