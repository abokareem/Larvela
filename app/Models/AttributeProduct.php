<?php
/**
 * \class	AttributeProduct
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-01-11
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
 * Maps a product with certain attributes when the product is a base/parent product with potentially lots of basic child products.
 *
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model to provide support for the "attribute_product" table.
 */
class AttributeProduct extends Model
{

/**
 * Define the table name specifically
 * @var string $table
 */
protected $table = "attribute_product";

/**
 * Indicates if the model should be timestamped.
 * @var boolean $timestamps
 */
public $timestamps = false;




/**
 * The attribute_product that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['attribute_id','product_id'];


	/**
	 * Maps attribute_product table using an Eloquent many to many function
	 *
	 * @return  mixed
	 */
	public function products()
	{
		return $this->belongsToMany('App\Models\Product','attribute_product','attribute_id','product_id');
	}
}
