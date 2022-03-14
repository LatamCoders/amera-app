<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('company_legal_name', 100)->unique();
            $table->string('dba', 100)->nullable();
            $table->string('tin', 100);
            $table->date('contract_start_date');
            $table->string('office_location_address', 100);
            $table->string('billing_address', 100);
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
        Schema::dropIfExists('corporate_accounts');
    }
}
