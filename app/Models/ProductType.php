<?php
/**
 * \class	ProductType
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2017-01-05
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model to provide all CRUD functionality for the "product_types" table.
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
protected $fillable = ['product_type'];


}
