<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
			$table->string('store_env_code')->unique();
			$table->string('store_name');
			$table->string('store_url');
			$table->string('store_currency')->default('AUD');
			$table->string('store_hours');
			$table->string('store_logo_filename');
			$table->string('store_logo_alt_text');
			$table->string('store_logo_thumb');
			$table->string('store_logo_invoice');
			$table->string('store_logo_email');
			$table->unsignedInteger('store_parent_id')->nullable();
			$table->string('store_status');
			$table->string('store_sales_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stores');
    }
}
