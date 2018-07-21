<?php
/**
 * \class	SubscriptionRequest
 * @date	2016-12-15
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Eloquent model for the "subscriptions" table.
 */
class SubscriptionRequest extends Model
{


/**
 * The table name (not in normal plural form)
 * @var string $table
 */
protected $table="subscription_requests";



/**
 * @var boolean $timestamps
 */
public $timestamps= false;


/**
 * The items that are mass assignable
 *
 * @var array $fillable
 */
protected $fillable = array('sr_email', 'sr_status', 'sr_process_value', 'sr_date_created', 'sr_date_updated');


}


