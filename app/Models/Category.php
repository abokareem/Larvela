<?php
/**
 * \class	Category
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-15
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
 * \brief MVC Model to provide support for the "category" table.
 *
 * {FIX_2016-09-06} removed some query builder code and replaced with Eloquent calls.
 * {FIX_2016-09-22} Changed table name to category
 */
class Category extends Model
{
/**
 * Tell framework the table is singular.
 * @var	string	$table
 */
public $table = "category";


/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * Items that are mass assignable
 * @var array $fillable
 */
protected $fillable = array('id','category_title','category_description','category_url','category_parent_id','category_status','category_visible','category_store_id');





	/**
	 * Returns all relevant images as a Collection of Image objects
	 *
	 * @return  mixed
	 */
	public function images()
	{
		return $this->belongsToMany('App\Models\Image','category_image','category_id','image_id');
	}
}
