<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('attribute_values', function (Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('attr_id');
			$table->string('attr_value');
			$table->integer('attr_sort_index');
			$table->foreign('attr_id')->references('id')->on('attributes');
		});
    }





    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('attribute_values');
    }
}
