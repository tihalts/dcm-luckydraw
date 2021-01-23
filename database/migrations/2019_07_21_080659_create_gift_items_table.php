<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique()->nullable();
            $table->timestamp('gift_at')->nullable();
            $table->timestamp('gifted_at')->nullable();
            $table->unsignedBigInteger('gift_id');
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
        Schema::dropIfExists('gift_items');
    }
}
