<?php
/**
 * \class	CartItem
 * \date	2016-0905
 * \author	Sid Young <sid@off-grid-engineering.com>
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
 * The CartItem table maps the cart ID and Product id's.
 */
namespace App\Models;
  

use Illuminate\Database\Eloquent\Model;



/**
 * \brief Cart item maps cart and product item
 */
class CartItem extends Model
{

	/**
	 * Map CartItem to Cart
	 *
	 * @return	mixed
	 */
	public function cart()
	{
		return $this->belongsTo('App\Models\Cart');
	}


	/**
	 * Maps a cart item product to products
	 *
	 * @return	mixed
	 */
	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}
}
