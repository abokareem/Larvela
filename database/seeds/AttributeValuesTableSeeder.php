<?php

use Illuminate\Database\Seeder;
use App\Models\Store;

class AttributeValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('attribute_values')->truncate();
		Store::create([ 'id'=>1, 'attr_id'=>1,'attrib_value'=>'BLACK','attr_sort_index'=>1]);
		Store::create([ 'id'=>2, 'attr_id'=>1,'attrib_value'=>'WHITE','attr_sort_index'=>2]);
		Store::create([ 'id'=>2, 'attr_id'=>1,'attrib_value'=>'RED','attr_sort_index'=>3]);
		Store::create([ 'id'=>2, 'attr_id'=>1,'attrib_value'=>'BLUE','attr_sort_index'=>4]);
    }
}
