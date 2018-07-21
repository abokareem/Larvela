<?php
/**
 * \class	Notification
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use App\Traits\Logger;

/**
 * \brief MVC Model that provides a CRUD layer access for the "notifications" table.
 */
class Notification extends Model
{
use Logger;


/**
 * Indicates if the model should be timestamped.
 *
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * The attributes that are mass assignable.
 *
 * @var array $fillable
 */
public $fillable =['product_code','email_address','date_created','time_created'];


}
