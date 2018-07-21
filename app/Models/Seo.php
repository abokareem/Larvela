<?php
/**
 * \class	Seo
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-07-29
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief MVC Model Contains CRUD methods for the "seo" table.
 *
 * {FIX_2017-10-25} Model Seo.php -Removed getByID call
 */
class Seo extends Model
{



/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * Name of databasetable needed by Eloquent calls
 * {FIX_2017-10-25} Model Seo.php - Added table name variable
 * @var string $table
 */
protected $table = "seo";


/**
 * The attributes that are mass assignable.
 * @var array $fillable
 */
public $fillable =['seo_token','seo_html_data'.'seo_status','seo_store_id','seo_edit'];


}
