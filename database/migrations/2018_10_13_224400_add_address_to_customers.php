<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('customers', function($table)
		{
			$table->string('customer_address')->default('');
			$table->string('customer_suburb')->default('');
			$table->string('customer_postcode')->default('');
			$table->string('customer_city')->default('');
			$table->string('customer_state')->default('');
			$table->string('customer_country')->default('');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('customers', function($table)
		{
			$table->dropColumn('customer_address');
			$table->dropColumn('customer_suburb');
			$table->dropColumn('customer_postcode');
			$table->dropColumn('customer_city');
			$table->dropColumn('customer_state');
			$table->dropColumn('customer_country');
		});
    }
}
