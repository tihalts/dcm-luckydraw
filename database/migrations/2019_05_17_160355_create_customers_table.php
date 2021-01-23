<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('cpr')->unique();
            $table->string('email')->unique();
            $table->string('phone_code')->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('password');
            $table->string('country_iso')->nullable();
            $table->string('nationality')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('created_user_id');
            $table->rememberToken();
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
        Schema::dropIfExists('customers');
    }
}
