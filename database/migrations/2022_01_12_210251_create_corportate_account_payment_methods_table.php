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
            $table->string('stripe_payment_method_id', 255)->nullable();
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
