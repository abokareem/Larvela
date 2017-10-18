<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->increments('id');
			$table->string('advert_name');
			$table->string('advert_html_code');
			$table->string('advert_status')->default('A');
			$table->date('advert_date_from');
			$table->date('advert_date_to');
			$table->integer('advert_store_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('adverts');
    }
}
