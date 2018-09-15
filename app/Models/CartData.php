<?php
/**
 * \class	CartData
 * \date	2017-09-07
 * \author	Sid Young
 * \version	1.0.0
 *
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
 * Cart Data holds additional data for the Cart table. This data is added to as a Customer progresses through
 * the checkout process. 
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \brief	Cart Data holds additional data for the cart that must transition from /cart to /shipping to /confirm etc
 */
class CartData extends Model
{

/**
 * Table name (not in correct format)
 * @var string $table
 */
protected $table = "cart_data";


/**
 * Columns that are mass assignable
 * @var array $fillable
 */
public $fillable = ['cd_cart_id','cd_payment_method','cd_shipping_method'];


	/**
	 * Map CartItem to Cart
	 *
	 * @return  mixed
	 */
	public function cart()
	{
		return $this->belongsTo('App\Models\Cart','cd_cart_id');
	}
}
