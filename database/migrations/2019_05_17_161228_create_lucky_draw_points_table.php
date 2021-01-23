<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLuckyDrawPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lucky_draw_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
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
        Schema::dropIfExists('lucky_draw_points');
    }
}
