<?php
/**
 * \class	 Store
 * \author	 Sid Young <sid@off-grid-engineering.com>
 * \date	 2016-08-23
 * \version	1.0.0
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
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model for the "stores" table.
 */
class Store extends Model
{

/**
 * The table name for Eloquent calls
 * @var string $table
 */
protected $table = "stores";


/**
 * The table does not use timestamps.
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * The items that are mass assignable
 * @var array $fillable
 */
protected $fillable = array(
		'store_env_code',
		'store_name',
		'store_url',
		'store_currency',
		'store_hours',
		'store_logo_filename',
		'store_logo_alt_text',
		'store_logo_thumb',
		'store_logo_invoice',
		'store_logo_email',
		'store_parent_id',
		'store_status',
		'store_sales_email',
		'store_address',
		'store_address2',
		'store_contact',
		'store_bg_image',
		'store_country',
		'store_country_code'
		);

protected $data = array();


	function __construct()
	{
	}



	/**
	 * Given a text string containing code, and a Store object containg row data
	 * return a text string with the codes translated into data and return.
	 *
	 *
	 * @deprecated Has been phased out, using Mailable interface.
	 *
	 *
	 * @param	string	$text
	 * @param	mixed	$store
	 * @return	string
	 */
	public function translate($text,$store)
	{
		$translations = array( 
			"{STORE_ID}"=>$store->id,
			"{STORE_ENV_CODE}"=>$store->store_env_code,
			"{STORE_NAME}"=>$store->store_name,
			"{STORE_URL}"=>$store->store_url,
			"{STORE_CURRENCY}"=>$store->store_currency,
			"{STORE_HOURS}"=>$store->store_hours,
			"{STORE_SALES_EMAIL}"=>$store->store_sales_email,
			"{STORE_CONTACT}"=>$store->store_contact,
			"{STORE_ADDRESS}"=>$store->store_address,
			"{STORE_ADDRESS2}"=>$store->store_address2
			);
		return str_replace(array_keys($translations), array_values($translations), $text);
	}

}
