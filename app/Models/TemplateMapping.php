<?php
/**
 * \class	TemplateMapping
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-18
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Map the template file name to a template "action" (business process) for the relevant store.
 *
 * Maps to a template action (using the full name) to the template for the store. 
 * Normally we would use template ID value and template name.
 * Note that action and store_id are a unique index in the table.
 */
class TemplateMapping extends Model
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
protected $fillable = ['template_name','template_action_id','template_store_id'];


}
