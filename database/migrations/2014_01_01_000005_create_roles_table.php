<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class CreateRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('role', function($table) {
			$table->increments('id');
			$table->string('name', 40);
			$table->string('description', 255);
			$table->timestamps();
		});

		DB::table('role')->insert(['id'=>1,'name'=>'root','description'=>'Store Administrator']);
		DB::table('role')->insert(['id'=>2,'name'=>'rdonly','description'=>'Store User']);
		DB::table('role')->insert(['id'=>3,'name'=>'customer','description'=>'Store Customer']);
		DB::table('role')->insert(['id'=>4,'name'=>'cron','description'=>'CRON Scheduler']);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('role');
	}

}
