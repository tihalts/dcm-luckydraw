<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('purchase_from' , 8 , 3)->default(0.000);
            $table->decimal('purchase_to' , 8 , 3)->default(0.000);
            $table->string('purchase_points');
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
        Schema::dropIfExists('point_settings');
    }
}
