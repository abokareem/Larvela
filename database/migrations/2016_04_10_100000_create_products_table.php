<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
			$table->string( 'prod_sku')->unique();
			$table->string( 'prod_title');
			$table->string( 'prod_short_desc');
			$table->string( 'prod_long_desc');
			$table->string( 'prod_visible');
			$table->integer('prod_weight');
			$table->integer('prod_qty')->default(0);
			$table->integer('prod_reorder_qty')->default(0);
			$table->decimal('prod_base_cost',13,2);
			$table->decimal('prod_retail_cost',13,2);
			$table->string( 'prod_combine_code');
			$table->string( 'prod_status');
			$table->date('prod_date_created');
			$table->time('prod_time_created');
			$table->date('prod_date_updated');
			$table->time('prod_time_updated');
			$table->date('prod_date_valid_from');
			$table->date('prod_date_valid_to');
			$table->integer('prod_type')->unsigned();
			$table->foreign('prod_type')->references('id')->on('product_types');
			$table->boolean('prod_has_free_shipping')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
