<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('category')->truncate();
		DB::table('category')->insert(['id'=>1,'category_url'=>'',
			'category_title'=>'Sale items', 'category_description'=>'Items on sale right now!',
			'category_status'=>'A', 'category_visible'=>'Y', 'category_store_id'=>1]);
		DB::table('category')->insert(['id'=>2,'category_url'=>'',
			'category_title'=>'Ex Demo Stock','category_description'=>'Ex-Demo Stock being cleared',
			'category_status'=>'A', 'category_visible'=>'Y', 'category_store_id'=>1]);
		DB::table('category')->insert(['id'=>3,'category_url'=>'',
			'category_title'=>'Clearance Stock','category_description'=>'Stock being cleared',
			'category_status'=>'A', 'category_visible'=>'Y', 'category_store_id'=>1]);
    }
}
