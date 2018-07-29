<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('subscription_stats', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('subs_completed')->unsigned();
			$table->integer('subs_deleted_count')->unsigned();
			$table->integer('subs_final_count')->unsigned();
			$table->integer('subs_resent_count')->unsigned();
			$table->date('subs_date_created')->default("0000-00-00");
			$table->unique('subs_date_created');
		});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('subscription_stats');
    }
}

