<?php
/**
 * \class	ProductType
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-01-05
 * \version	1.0.1
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
 * \brief MVC Model to provide all CRUD functionality for the "product_types" table.
 *
 * {INFO_2018-09-18} - ProductType.php Added new column to seeds, migration and DB
 */
class ProductType extends Model
{


/**
 *
 * @var string $table
 */
protected $table="product_types";


/**
 * Flag that we ar enot suing timestamped columns
 * @var boolean $timestamps
 */
public $timestamps = false;


/**
 * Columns that are mass asignable
 * @var array $fillable
 */
protected $fillable = ['product_type','product_type_token'];


}
