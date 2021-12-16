<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate');
            $table->text('comments');
            $table->foreignId('selfpay_id')->nullable()->constrained('selfpay');
            $table->foreignId('booking_id')->nullable()->constrained('booking');
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
        Schema::dropIfExists('driver_rates');
    }
}
