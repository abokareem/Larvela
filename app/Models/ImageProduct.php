<?php
/**
 * \class	ImageProduct
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-07-29
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
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide CRUD layer access to the "image_product" pivot table,
 *
 * - Maps "base images" to products.
 * - Does not map thumbnails, these are still located in the "images" table but referenced using the image_parent_id field.
 */
class ImageProduct extends Model
{

/**
 * Pivot table mapping images and products
 * @var string $table
 */
protected $table = "image_product";

/**
 * Indicates if the model should be timestamped.
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['image_id','product_id'];






	/**
	 * Maps a product ID in locks table to products table.
	 *
	 * @return  mixed
	 */
	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}



	/**
	 * Maps a product ID to products table.
	 *
	 * @return  mixed
	 */
	public function image()
	{
		return $this->belongsTo('App\Models\Image');
	}
}
