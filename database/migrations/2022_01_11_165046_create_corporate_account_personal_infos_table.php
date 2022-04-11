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
            $table->string('telephone_number', 30)->unique();
            $table->string('fax_number', 30)->nullable();
            $table->string('email', 50)->unique();
            $table->string('website', 50);
            $table->string('additional_contact_name', 50)->nullable();
            $table->string('additional_contact_number', 50)->nullable();
            $table->string('additional_contact_email', 50)->nullable();
            $table->string('additional_contact_title', 50)->nullable();
            $table->foreignId('corporate_account_id')->constrained('corporate_accounts')->onDelete('cascade');
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
