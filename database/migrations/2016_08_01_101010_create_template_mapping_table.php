<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateMappingTable extends Migration
{
    public function up()
    {
		Schema::create('template_mapping', function($table)
		{
			$table->increments('id');
			$table->string('template_name');
			$table->integer('template_action_id');
			$table->integer('template_store_id');
			$table->unique(array('template_action_id','template_store_id'));
		});
    }

    public function down()
    {
		Schema::drop('template_mapping');
    }
}
