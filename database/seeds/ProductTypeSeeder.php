<?php

use Illuminate\Database\Seeder;
use App\Models\ProductType;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('product_types')->truncate();
		ProductType::create(['id'=>1,'product_type'=>'Basic Product','product_type_token'=>'BASIC']);
		ProductType::create(['id'=>2,'product_type'=>'Parent Product','product_type_token'=>'PARENT']);
		ProductType::create(['id'=>3,'product_type'=>'Virtual Product (Limited)','product_type_token'=>'VLIMITED']);
		ProductType::create(['id'=>4,'product_type'=>'Virtual Product (Unlimited)','product_type_token'=>'VUNLIMITED']);
		ProductType::create(['id'=>5,'product_type'=>'Pack Product','product_type_token'=>'PACK']);
    }
}
