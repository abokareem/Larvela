<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('seo', function (Blueprint $table) {
			$table->increments('id');
			$table->string('seo_token',255);
			$table->string('seo_html_data',8192);
			$table->string('seo_status',1);
			$table->string('seo_edit',1);
			$table->unsignedInteger('seo_store_id')->nullable();
			$table->unique(array('seo_token','seo_store_id'));
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('seo');
    }
}
