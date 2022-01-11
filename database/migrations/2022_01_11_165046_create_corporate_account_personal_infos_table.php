<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateAccountPersonalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_account_personal_infos', function (Blueprint $table) {
            $table->id();
            $table->string('telephone_number', 30);
            $table->string('fax_number', 30)->nullable();
            $table->string('email', 50);
            $table->string('website', 50)->nullable();
            $table->string('contact_name', 50);
            $table->string('contact_number', 50);
            $table->foreignId('corporate_account_id')->nullable()->constrained('corporate_accounts');
            $table->foreignId('user_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_account_personal_infos');
    }
}