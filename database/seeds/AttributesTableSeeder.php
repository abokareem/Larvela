<?php

use Illuminate\Database\Seeder;
use App\Models\Attribute;

class AttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('attributes')->truncate();
		Attribute::create([ 'id'=>1, 'attribute_name'=>"Colour",'attribute_token'=>'COLOUR','store_id'=>1]);
		Attribute::create([ 'id'=>2, 'attribute_name'=>"Size",'attribute_token'=>'SIZE','store_id'=>1]);
    }
}
