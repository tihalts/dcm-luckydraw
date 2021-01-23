<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexForAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_templates', function(Blueprint $table){
            $table->index('campaign_id');
        });
        Schema::table('customer_vouchers', function(Blueprint $table){
            $table->index('customer_id');
            $table->index('purchase_id');
            $table->index('voucher_id');
        });
        Schema::table('gifts', function(Blueprint $table){
            $table->index('campaign_id');
        });
        Schema::table('gift_items', function(Blueprint $table){
            $table->index('gift_id');
        });
        Schema::table('lucky_draw_points', function(Blueprint $table){
            $table->index('customer_id');
            $table->index('purchase_id');
        });
        Schema::table('purchases', function(Blueprint $table){
            $table->index('customer_id');
            $table->index('user_id');
            $table->index('shop_id');
        });
        Schema::table('raffle_draw_settings', function(Blueprint $table){
            $table->index('lucky_draw_id');
        });
        Schema::table('scratch_cards', function(Blueprint $table){
            $table->index('customer_id');
            $table->index('campaign_id');
            $table->index('user_id');
            $table->index('gift_id');
            $table->index('code');
        });
        Schema::table('scratch_card_requests', function(Blueprint $table){
            $table->index('customer_id');
            $table->index('campaign_id');
        });
        Schema::table('spinner_items', function(Blueprint $table){
            $table->index('gift_id');
            $table->index('spinner_id');
        });
        Schema::table('spin_gifts', function(Blueprint $table){
            $table->index('spinner_id');
        });
        Schema::table('spin_gift_items', function(Blueprint $table){
            $table->index('gift_id');
        });
        Schema::table('spin_winners', function(Blueprint $table){
            $table->index('spinner_id');
            $table->index('customer_id');
            $table->index('user_id');
            $table->index('gift_id');
        });
        Schema::table('user_actions', function(Blueprint $table){
            $table->index('customer_id');
        });
        Schema::table('vouchers', function(Blueprint $table){
            $table->index('customer_id');
            $table->index('user_id');
            $table->index('campaign_id');
        });
        Schema::table('winners', function(Blueprint $table){
            $table->index('customer_id');
            $table->index('lucky_draw_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_templates', function (Blueprint $table){
            $table->dropIndex(['campaign_id']);
        });
        Schema::table('customer_vouchers', function (Blueprint $table){
            $table->dropIndex('customer_id');
            $table->dropIndex('purchase_id');
            $table->dropIndex('voucher_id');
        });
        Schema::table('gifts', function(Blueprint $table){
            $table->dropIndex('campaign_id');
        });
        Schema::table('gift_items', function(Blueprint $table){
            $table->dropIndex('gift_id');
        });
        Schema::table('lucky_draw_points', function(Blueprint $table){
            $table->dropIndex('customer_id');
            $table->dropIndex('purchase_id');
        });
        Schema::table('purchases', function(Blueprint $table){
            $table->dropIndex('customer_id');
            $table->dropIndex('user_id');
            $table->dropIndex('shop_id');
        });
        Schema::table('raffle_draw_settings', function(Blueprint $table){
            $table->dropIndex('lucky_draw_id');
        });
        Schema::table('scratch_cards', function(Blueprint $table){
            $table->dropIndex('customer_id');
            $table->dropIndex('campaign_id');
            $table->dropIndex('user_id');
            $table->dropIndex('gift_id');
            $table->dropIndex('code');
        });
        Schema::table('scratch_card_requests', function(Blueprint $table){
            $table->dropIndex('customer_id');
            $table->dropIndex('campaign_id');
        });
        Schema::table('spinner_items', function(Blueprint $table){
            $table->dropIndex('gift_id');
            $table->dropIndex('spinner_id');
        });  
        Schema::table('spin_gifts', function(Blueprint $table){
            $table->dropIndex('spinner_id');
        });
        Schema::table('spin_gift_items', function(Blueprint $table){
            $table->dropIndex('gift_id');
        });
        Schema::table('spin_winners', function(Blueprint $table){
            $table->dropIndex('spinner_id');
            $table->dropIndex('customer_id');
            $table->dropIndex('user_id');
            $table->dropIndex('gift_id');
        });
        Schema::table('user_actions', function(Blueprint $table){
            $table->dropIndex('customer_id');
        });
        Schema::table('vouchers', function(Blueprint $table){
            $table->dropIndex('customer_id');
            $table->dropIndex('user_id');
            $table->dropIndex('campaign_id');
        });
        Schema::table('winners', function(Blueprint $table){
            $table->dropIndex('customer_id');
            $table->dropIndex('lucky_draw_id');
        });
    }
}
