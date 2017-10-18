<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cd_cart_id')->unsigned();
			$table->decimal('cd_sub_total',13,2);
			$table->decimal('cd_tax',13,2);
			$table->decimal('cd_shipping',13,2);
			$table->decimal('cd_total',13,2);
            $table->string('cd_shipping_method');
            $table->string('cd_payment_method');
            $table->timestamps();

			$table->foreign('cd_cart_id')->references('id')->on('carts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_data');
    }
}
