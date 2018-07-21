<?php
/**
 * \class	SubscriptionStat
 * @date	2018-03-11
 * @author	Sid Young <sid@off-grid-engineering.com>
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Eloquent model for the "subscription_stats" table.
 */
class SubscriptionStat extends Model
{


/**
 * The table name (not in normal plural form)
 * @var string $table
 */
protected $table="subscription_stats";



/**
 * @var boolean $timestamps
 */
public $timestamps= false;


/**
 * The items that are mass assignable
 *
 * @var array $fillable
 */
protected $fillable = array('sub_completed','sub_final_count','sub_deleted_count','sub_resent_count','sub_date_created');


}
