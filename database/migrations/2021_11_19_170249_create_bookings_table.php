<?php

use App\utils\UniqueIdentifier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id', 50)->unique();
            $table->foreignId('selfpay_id')->constrained('selfpay');
            $table->dateTime('booking_date');
            $table->time('pickup_time');
            $table->text('city');
            $table->string('surgery_type', 100);
            $table->dateTime('appoinment_datetime');
            $table->text('from');
            $table->text('to');
            $table->dateTime('trip_start');
            $table->dateTime('trip_end');
            $table->foreignId('driver_id')->nullable()->constrained('drivers');

            $table->tinyInteger('status');
            $table->foreign('status')->references('code')->on('status_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
