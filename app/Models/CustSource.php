<?php
/**
 * \class	CustSource
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 *
 *
 *  [CC]
 */
namespace App\Models;



use Illuminate\Database\Eloquent\Model;


/**
 * \brief Model for managing the Customer Referral Source
 * i.e. WEBSITE, EBAY etc
 */
class CustSource extends Model
{


protected $table = "cust_sources";

/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
public $fillable =['cs_name'];

}



	
