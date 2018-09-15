<?php
/**
 * \class	Cart
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-09-01
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
 *
 * The Cart table maps the cart and logged in User (Customer) and is the parent for the other CartXXX related Models.
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Maps cart to user and records access times.
 * user_id, created_at, updated_at
 */
class Cart extends Model
{

	/**
	 * Maps cart to users table
	 *
	 * @return	mixed
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}




	/**
	 * Maps cart to Cart Items
	 *
	 * @return	mixed
	 */
	public function cartItems()
	{
		return $this->hasMany('App\Models\CartItem');
	}

}
