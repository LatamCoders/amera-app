<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('driver');
            $table->text('vehicle_front_image');
            $table->tinyInteger('vehicle_front_image_check')->default(0);
            $table->text('vehicle_rear_image');
            $table->tinyInteger('vehicle_rear_image_check')->default(0);
            $table->text('vehicle_side_image');
            $table->tinyInteger('vehicle_side_image_check')->default(0);
            $table->text('vehicle_interior_image');
            $table->tinyInteger('vehicle_interior_image_check')->default(0);
            $table->text('driver_license');
            $table->tinyInteger('driver_license_check')->default(0);
            $table->text('proof_of_insurance');
            $table->tinyInteger('proof_of_insurance_check')->default(0);
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
        Schema::dropIfExists('vehicle_documents');
    }
}
