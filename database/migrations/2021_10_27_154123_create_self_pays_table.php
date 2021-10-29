<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelfPaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selfpay', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('lastname', 50);
            $table->string('phone_number', 50);
            $table->string('email', 50);
            $table->string('profile_picture', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('self_pays');
    }
}
