<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateInCampaignGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_groups', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->timestamp('start_at')->nullable()->after('description');
            $table->timestamp('end_at')->nullable()->after('start_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_groups', function (Blueprint $table) {
            //
        });
    }
}
