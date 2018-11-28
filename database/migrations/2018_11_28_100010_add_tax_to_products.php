<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('products', function($table)
		{
			$table->boolean('prod_is_taxable')->default(0);
			$table->decimal('prod_tax_rate',13,2);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('products', function($table)
		{
			$table->dropColumn('prod_is_taxable');
			$table->dropColumn('prod_tax_rate');
		});
    }
}
