<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
			$table->string('product_code')->index('notify_code_idx');
			$table->string('email_address')->index('notify_email_idx');
			$table->date('date_created')->index('notify_date_idx');
			$table->time('time_created');
			$table->unique(array('product_code','email_address'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notifications');
    }
}
