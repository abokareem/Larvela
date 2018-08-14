<?php
/**
 * \class	StoreHelper
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date 	2016-08-18
 * \version 1.0.0
 *
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * \addtogroup Helpers
 * StoreHelper - Provides a static method of accessing store data.
 * - This code will be deprecated.
 */
namespace App\Helpers;


use App\Models\Store;
use App\Models\Category;



/**
 * \brief Static class that can be used anywhere to access and render stores text.
 *
 * Made available thanks to class alias in config/app.php
 */
class StoreHelper
{

	/**
	 * Construct and return a HTML unordered list
	 *
	 * @return string HTML list
	 */
	public static function CategoryMenu()
	{
		$Category = new Category;
		
		$Store = new Store;
		if(($store_code=getenv("STORE_CODE"))!=false)
		{
			$store = Store::where('store_env_code', $store_code )->first();
			$Category->BuildStoreData($store->id);
			return $Category->getHTML();
		}
		return $Category->getHTML();
	}







	/**
	 * Return either a Database row from the stores table
	 * or a dynamically built object for a DEMO store.
	 *
	 * @post You must check the store_code field for the word "DEMO" before using.
	 * @return mixed Collection object
	 */
	public static function StoreData()
	{
		$Store = new Store;

		$store = new \stdClass;
		if(($store_code=getenv("STORE_CODE"))!=false)
		{
			return Store::where('store_env_code', $store_code )->first();
		}
		else
		{
			$store->store_address = "Demo Street";
			$store->store_address2 = "Demo Village";
			$store->store_contact = "Demo Store Owner";
			$store->store_sales_email = "help@demo-store.com";
			$store->store_name = "Demo Store";
			$store->store_code = "DEMO";
			$store->store_url = "localhost";
			$store->id = 0;
			$store->store_hours = "closed";
			$store->store_logo_filename = "";
			$store->store_logo_thumb = "";
			$store->store_logo_invoice ="";
			$store->store_logo_email ="";
		}
		return $store;
	}



	/**
	 * Given a body of text, str replace all the fields from the stores table and return the text.
	 *
	 * @param $template string the stirng of text that needs to be checked.
	 * @return string Text string with all fields translated to text.
	 *
	 * store_env_code 
	 * store_name
	 * store_url
	 * store_currency
	 * store_hours  
	 * store_logo_filename
	 * store_logo_alt_text
	 * store_logo_thumb 
	 * store_logo_invoice
	 * store_logo_email 
	 * store_parent_id
	 * store_status store_sales_email store_contact store_address store_address2 store_bg_image
	 */
	public static function StrReplace($template)
	{
		$row = self::StoreData();
		
		$t1 = str_replace("{STORE_NAME}",$row->store_name, $template);
		$t2 = str_replace("{STORE_URL}",$row->store_url, $t1);
		$t3 = str_replace("{STORE_CURRENCY}",$row->store_currency, $t2);
		$t4 = str_replace("{STORE_HOURS}",$row->store_hours, $t3);
		$t5 = str_replace("{STORE_SALES_EMAIL}",$row->store_sales_email, $t4);
		$t6 = str_replace("{STORE_CONTACT}",$row->store_contact, $t5);
		$t7 = str_replace("{STORE_ADDRESS}",$row->store_address, $t6);
		$t8 = str_replace("{STORE_ADDRESS2}",$row->store_address2, $t7);
		$t9 = str_replace("{STORE_LOGO_FILENAME}",$row->store_logo_filename, $t8);
		$t1 = str_replace("{STORE_LOGO_THUMB}",$row->store_logo_thumb, $t9);
		$t2 = str_replace("{STORE_LOGO_INVOICE}",$row->store_logo_invoice, $t1);
		$t3 = str_replace("{STORE_LOGO_EMAIL}",$row->store_logo_email, $t2);
		return $t3;
	}
}
