<?php



use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class RoleTableSeeder extends Seeder{

public $table="roles";

    public function run()
    {

        if (App::environment() === 'production') {
            exit('I just stopped you getting fired. Love, Amo.');
        }

        DB::table('roles')->truncate();

        Role::create(['id'=>1,'role_name'=>'ADMIN','role_description'=>'System Administrator']);
		Role::create(['id'=>2,'role_name'=>'CUSTOMER','role_description'=>'Store Customer.']);
        Role::create(['id'=>3,'role_name'=>'USER','role_description'=>'user of backend system.']);
        Role::create(['id'=>4,'role_name'=>'ORDERS','role_description'=>'Order processing']);
        Role::create(['id'=>5,'role_name'=>'ACCOUNTS','role_description'=>'Account processing']);
        Role::create(['id'=>6,'role_name'=>'DISPATCH','role_description'=>'Dispatch processing']);
        Role::create(['id'=>7,'role_name'=>'SALES','role_description'=>'Sales Access']);
        Role::create(['id'=>8,'role_name'=>'PRODUCTADMIN','role_description'=>'Product Administration']);
        Role::create(['id'=>9,'role_name'=>'CATEGORYADMIN','role_description'=>'Category Administration']);
        Role::create(['id'=>10,'role_name'=>'SALESADMIN','role_description'=>'Sales Administration']);
    }

}
