<?php
/**
 * \class	StoreSettings
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
 * \brief MVC Eloquent model for the "store_settings" table.
 */
class StoreSetting extends Model
{


/**
 * @var string $table;
 */
protected $table="store_settings";


/**
 * @var boolean $timestamps
 */
public $timestamps= false;


/**
 * The items that are mass assignable
 * @var array $fillable
 */
protected $fillable = array('setting_name','setting_value','setting_store_id');

}
