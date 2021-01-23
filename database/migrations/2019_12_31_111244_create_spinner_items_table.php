<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpinnerItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spinner_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('bg_color')->nullable();
            $table->string('text_color')->nullable();
            $table->unsignedBigInteger('gift_id')->nullable();
            $table->unsignedBigInteger('spinner_id')->nullable();
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
        Schema::dropIfExists('spinner_items');
    }
}
