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
		Store::create([
			'id'=>1,
			'store_env_code'=>'DEMO',
			'store_name'=>'Demo Store',
			'store_url'=>'https://larvela.org',
			'store_currency'=>'AUD',
			'store_hours'=>'M-F 10am-8pm',
			'store_status'=>'A',
			'store_sales_email'=>'sales@localhost',
			'store_contact'=>'Sales Team',
			'store_address'=>'Call for Address',
			'store_address2'=>'Australia',
			'store_country'=>'Australia',
			'store_country_code'=>'AU'
			]);
    }
}
