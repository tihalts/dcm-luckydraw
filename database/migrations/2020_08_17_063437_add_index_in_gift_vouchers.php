<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexInGiftVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_vouchers', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('campaign_id');
            $table->index('user_id');
            $table->index('code');
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
            $table->dropIndex(['customer_id' , 'campaign_id' , 'user_id' , 'code']);
        });
    }
}
