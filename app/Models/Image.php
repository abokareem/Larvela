<?php
/**
 * \class	Image
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
 *
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent model that uses the "images" table.
 *
 * {FIX_2017-10-31} Image.php - Began refactoring the code.
 */
class Image extends Model
{


/**
 * Table name
 * @var string $table
 */
protected $table = "images";




/**
 * Timestamps are not used in the table.
 * @var boolean $timestamps
 */
public $timestamps = false;



/**
 * Items that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['image_file_name','image_folder_name','image_size','image_height','image_width','image_order','image_parent_id'];








	/**
	 * Return a collection of images which are children of this parent.
	 *
	 * @return	mixed
	 */
	public function thumbnails()
	{
		return $this->hasMany('App\Models\Image','image_parent_id');
	}





	/**
	 * Maps the image to the image_product table using an Eloquent many to many function
	 *
	 * @return 	mixed
	 */
	public function products()
	{
		return $this->belongsToMany('App\Models\Product','image_product','image_id','product_id');
	}


	public function categories()
	{
		return $this->belongsToMany('App\Models\Category');
	}
}


