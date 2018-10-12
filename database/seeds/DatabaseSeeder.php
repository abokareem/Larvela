<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    	$this->call(ProductTypeSeeder::class);
    	$this->call(RoleTableSeeder::class);
    	$this->call(StoresTableSeeder::class);
    	$this->call(AttributeValuesTableSeeder::class);
    	$this->call(CustomerSourcesTableSeeder::class);
    }
}
