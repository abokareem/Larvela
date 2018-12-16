<?php



use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;

class CurrencyTableSeeder extends Seeder{

public $table="currencies";

    public function run()
    {

        if (App::environment() === 'production') {
            exit('Exiting due to production environment!');
        }

        DB::table($table)->truncate();

        Currency::create(['id'=>1,'currency_code'=>'AUD','currency_name'=>'Australian Dollar','currency_numeric'=>1]);
        Currency::create(['id'=>2,'currency_code'=>'CAD','currency_name'=>'Canadian Dollar','currency_numeric'=>2]);
        Currency::create(['id'=>3,'currency_code'=>'EUR','currency_name'=>'Euro','currency_numeric'=>3]);
        Currency::create(['id'=>4,'currency_code'=>'GBP','currency_name'=>'Great British Pound','currency_numeric'=>4]);
        Currency::create(['id'=>5,'currency_code'=>'JPY','currency_name'=>'Japanese Yen','currency_numeric'=>5]);
        Currency::create(['id'=>6,'currency_code'=>'NZD','currency_name'=>'New Zealand Dollar','currency_numeric'=>6]);
        Currency::create(['id'=>7,'currency_code'=>'RMB','currency_name'=>'Renminbi','currency_numeric'=>7]);
        Currency::create(['id'=>8,'currency_code'=>'CNY','currency_name'=>'Chinese Yuan','currency_numeric'=>8]);
        Currency::create(['id'=>9,'currency_code'=>'SGD','currency_name'=>'Singapore dollar','currency_numeric'=>9]);
        Currency::create(['id'=>10,'currency_code'=>'USD','currency_name'=>'United States Dollar','currency_numeric'=>10]);
    }
}
