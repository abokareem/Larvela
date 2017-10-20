<?php

use Illuminate\Database\Seeder;
use App\Models\Store;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('stores')->truncate();
		Store::create(['id'=>1,'store_name'=>'Demo Store']);
    }
}
