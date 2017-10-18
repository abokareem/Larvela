<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_product', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('image_id')->unsigned();
			$table->foreign('image_id')->references('id')->on('images');
			$table->integer('product_id')->unsigned();
			$table->foreign('product_id')->references('id')->on('products');
			$table->unique(array('product_id','image_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('image_product');
    }
}
