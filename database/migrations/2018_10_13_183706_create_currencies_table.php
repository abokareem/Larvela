<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable  extends  Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('currencies', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('currency_code')->unique();
			$table->string('currency_name');
			$table->string('currency_numeric')->unique();
		});
    }




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currencies');
    }
}
