<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemTable extends Migration
{
    public function up()
    {
        Schema::create('order_items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order_item_oid')->unsigned();
			$table->string('order_item_sku')->index('oi_sku_idx');
			$table->string('order_item_desc');
			$table->string('order_item_email')->index('oi_email_idx');
			$table->integer('order_item_qty_purchased')->default(0);
			$table->integer('order_item_qty_supplied')->default(0);
			$table->integer('order_item_qty_backorder')->default(0);
			$table->string('order_item_dispatch_status')->default('W');
			$table->decimal('order_item_price_status',13,2);
			$table->date('order_item_date')->index('oi_date_idx');
			$table->time('order_item_time');
			$table->timestamps();
		
			$table->foreign('order_item_oid')->references('id')->on('orders');
  
  		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
