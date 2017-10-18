<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionRequestTable extends Migration
{
    /**
     * Run the migrations.
     *sr_email', 'sr_status', 'sr_process_value', 'sr_date_created', 'sr_date_updated'
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_request', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sr_email')->unique();
            $table->string('sr_status')->default('W');
			$table->integer('sr_process_value')->default(0);
			$table->date('sr_date_created');
			$table->date('sr_date_updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subscription_request');
	}
}
