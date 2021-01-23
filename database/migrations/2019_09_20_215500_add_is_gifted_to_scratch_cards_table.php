<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsGiftedToScratchCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->boolean('is_gifted')->default(false)->after('scratched_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scratch_cards', function (Blueprint $table) {
            //
        });
    }
}
