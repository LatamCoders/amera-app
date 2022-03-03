<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorportateAccountPaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corportate_account_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name_on_cc', 50);
            $table->string('cc_number', 100);
            $table->string('type_of_cc', 50);
            $table->string('code_of_cc', 10);
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
        Schema::dropIfExists('corportate_account_payment_methods');
    }
}
