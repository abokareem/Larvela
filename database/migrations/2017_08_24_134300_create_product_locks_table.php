<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('product_locks', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('product_lock_pid')->unsigned();
			$table->integer('product_lock_cid')->unsigned();
			$table->integer('product_lock_qty')->default(1);
			$table->integer('product_lock_utime')->unsigned();

			$table->foreign('product_lock_pid')->references('id')->on('products');
			$table->foreign('product_lock_cid')->references('id')->on('carts');

			$table->unique(array('product_lock_pid','product_lock_cid'));
		});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('product_types');
    }
}

