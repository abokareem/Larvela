<?php
/**
 * \class	Advert
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-15
 * @package App\Models
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model to provide support for the "adverts" table.
 *
 * - Adverts are HTML code displayed in the product grid on the storefront.
 * - a date can be specified to control visability.
 * - only "A"ctive entries should be displayed.
 */
class Advert extends Model
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
protected $fillable = ['advert_name','advert_html_code','advert_status','advert_store_id','advert_date_from','advert_date_to'];


}
