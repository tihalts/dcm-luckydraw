<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGiftIdInGiftVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('gift_id')->after('id');
            $table->boolean('is_gifted')->default(false)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_vouchers', function (Blueprint $table) {
            //
        });
    }
}
