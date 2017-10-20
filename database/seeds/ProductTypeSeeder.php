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
		ProductType::create(['id'=>1,'product_type'=>'Basic Product']);
		ProductType::create(['id'=>2,'product_type'=>'Parent Product']);
		ProductType::create(['id'=>3,'product_type'=>'Virtual Product (Limited)']);
		ProductType::create(['id'=>4,'product_type'=>'Virtual Product (Unlimited)']);
    }
}
