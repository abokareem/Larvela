<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_image', function (Blueprint $table) {
			$table->integer('category_id')->unsigned();
			$table->foreign('category_id')->references('id')->on('category');
			$table->integer('image_id')->unsigned();
			$table->foreign('image_id')->references('id')->on('images');
			$table->unique(array('image_id','category_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('category_image');
    }
}
