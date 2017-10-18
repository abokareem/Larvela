<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_name')->default('');
            $table->string('customer_email')->unique();
            $table->string('customer_mobile');
            $table->string('customer_status')->default('A');
			$table->integer('customer_source_id')->unsigned();
			$table->integer('customer_store_id')->unsigned();
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
        Schema::drop('customers');
    }
}
