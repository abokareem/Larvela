<?php



use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\CustSource;

class CustomerSourcesTableSeeder extends Seeder{

public $table="cust_sources";

    public function run()
    {

        if (App::environment() === 'production') {
            exit('I just stopped you getting fired. Love, Amo.');
        }

        DB::table('cust_sources')->truncate();

        CustSource::create(['id'=>1,'cs_name'=>'EBAY']);
        CustSource::create(['id'=>2,'cs_name'=>'WEBSTORE']); 
        CustSource::create(['id'=>3,'cs_name'=>'SUBSCRIBERADMIN']);
    }

}
