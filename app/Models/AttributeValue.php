<?php
/**
 * \class	AttributeValue
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-01-13
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model to provide support for the "attribute_values" table.
 */
class AttributeValue extends Model
{
/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;




/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
protected $fillable = ['attribute_name'];


}
