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
            $table->string('client_id', 50)->unique();
            $table->string('name', 50);
            $table->string('lastname', 50);
            $table->string('gender', 50)->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone_number', 50)->unique();
            $table->string('email', 50);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->text('note')->nullable();
            $table->text('profile_picture')->nullable();
            $table->foreignId('ca_id')->nullable()->constrained('corporate_accounts')->nullOnDelete();
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
    public
    function down()
    {
        Schema::dropIfExists('self_pays');
    }
}
