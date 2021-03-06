<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmeraAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amera_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user');
            $table->string('email')->unique();
            $table->foreignId('amera_user_id')->constrained('amera_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amera_admins');
    }
}
