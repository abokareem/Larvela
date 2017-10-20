<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('orders', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_ref');
			$table->string('order_src')->default(0);
			$table->integer('order_cart_id')->unsigned();
			$table->foreign('order_cart_id')->references('id')->on('carts');
			$table->integer('order_cid')->unsigned();
			$table->foreign('order_cid')->references('id')->on('customers');
			$table->char('order_status',2)->default('W');
			$table->char('order_payment_status',1)->default('W');
			$table->char('order_dispatch_status',1)->default('W');
			$table->decimal('order_value',3,2);
			$table->decimal('order_shipping_value',13,2);
			$table->string('order_shipping_method');
			$table->date('order_date')->default('0000-00-00');
			$table->time('order_time')->default('00:00:00');
			$table->date('order_dispatch_date')->default('0000-00-00');
			$table->time('order_dispatch_time')->default('00:00:00');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('order');
    }
}
