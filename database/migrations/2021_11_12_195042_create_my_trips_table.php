<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_trips', function (Blueprint $table) {
            $table->id();
            $table->string('from', 50);
            $table->string('to', 50);
            $table->dateTime('trip_start');
            $table->dateTime('trip_end');
            $table->foreignId('selfpay_id')->constrained('selfpay');
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
        Schema::dropIfExists('my_trips');
    }
}
