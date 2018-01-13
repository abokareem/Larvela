<?php
/**
 * \class	Attribute
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-01-12
 * @package App\Models
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model to provide support for the "attributes" table.
 */
class Attribute extends Model
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
protected $fillable = ['attribute_name','stroe_id'];


}
