<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_cid');
            $table->string('customer_email')->default('undefined');
            $table->string('customer_address')->default('undefined');
            $table->string('customer_suburb')->default('Brisbane');;
            $table->string('customer_postcode')->default('4000');
            $table->string('customer_state')->default('QLD');
            $table->string('customer_country')->default('AU');
            $table->string('customer_status')->default('A');
			$table->date('customer_date_created');
			$table->date('customer_date_updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customer_addresses');
    }
}
