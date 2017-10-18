<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('attributes', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('attribute_name');
			$table->unsignedInteger('store_id')->nullable();
		});
		DB::table('attributes')->insert(['id'=>1,'attribute_name'=>'Colour','store_id'=>0]);
		DB::table('attributes')->insert(['id'=>2,'attribute_name'=>'Size','store_id'=>0]);
    }





    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('attributes');
    }
}
