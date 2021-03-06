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
            $table->foreignId('selfpay_id')->constrained('selfpay')->cascadeOnDelete();
            $table->dateTime('booking_date');
            $table->time('pickup_time');
            $table->text('city');
            $table->string('surgery_type', 100);
            $table->dateTime('appoinment_datetime');
            $table->text('from');
            $table->text('to');
            $table->string('facility_name', 50);
            $table->string('doctor_name', 50);
            $table->string('facility_phone_number', 50);
            $table->string('approximately_return_time', 20);
            $table->dateTime('trip_start')->nullable();
            $table->dateTime('trip_end')->nullable();
            $table->double('price');
            $table->double('service_fee');
            $table->string('charge_id', 100)->nullable();
            $table->boolean('refund')->nullable();
            $table->dateTime('booking_paid_to_driver_at')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();

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
