<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelfPayRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_pay_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate');
            $table->text('comments');
            $table->foreignId('selfpay_id')->nullable()->constrained('selfpay');
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('booking_id')->nullable()->constrained('bookings');
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
        Schema::dropIfExists('self_pay_rates');
    }
}
