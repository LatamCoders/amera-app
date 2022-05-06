<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
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
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->text('vehicle_front_image');
            $table->dateTime('vehicle_front_image_verify_at')->nullable();
            $table->text('vehicle_rear_image');
            $table->dateTime('vehicle_rear_image_verify_at')->nullable();
            $table->text('vehicle_side_image');
            $table->dateTime('vehicle_side_image_verify_at')->nullable();
            $table->text('vehicle_interior_image');
            $table->dateTime('vehicle_interior_image_verify_at')->nullable();
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
