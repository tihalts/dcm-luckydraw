<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRaffleDrawSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raffle_draw_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('min_amount')->default(0)->after('data');
            $table->unsignedSmallInteger('max_points')->default(0)->after('min_amount');
            $table->boolean('send_sms')->default(false)->after('max_points');
            $table->boolean('send_email')->default(false)->after('send_sms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raffle_draw_settings', function (Blueprint $table) {
            //
        });
    }
}
