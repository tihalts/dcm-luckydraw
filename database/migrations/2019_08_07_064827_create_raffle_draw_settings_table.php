<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaffleDrawSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raffle_draw_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lucky_draw_id');
            $table->text('email')->nullable();
            $table->text('sms')->nullable();
            $table->string('image')->nullable();
            $table->text('data')->nullable();
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
        Schema::dropIfExists('raffle_draw_settings');
    }
}
