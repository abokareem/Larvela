<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->increments('id');
			$table->string('category_url');
			$table->string('category_title');
			$table->string('category_description');
			$table->unsignedInteger('category_parent_id')->nullable();
			$table->string('category_status');
			$table->string('category_visible');
			$table->unsignedInteger('category_store_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('category');
    }
}
