<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('driver_id', 50)->unique();
            $table->string('name', 50);
            $table->string('lastname', 50);
            $table->string('gender', 50)->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone_number', 50)->unique()->nullable();
            $table->string('email', 50);
            $table->text('address')->nullable();
            $table->boolean('is_cna');
            $table->text('profile_picture')->nullable();
            $table->text('user_device_id')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->dateTime('phone_number_verified_at')->nullable();
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
        Schema::dropIfExists('drivers');
    }
}
