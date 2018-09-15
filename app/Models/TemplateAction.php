<?php
/**
 * \class	TemplateAction
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-18
 *
 * @deprecated TemplateAction class now deprecated due to use of Mailables.
 *
 * [CC]
 *
 *
 * \addtogroup PENDING_REMOVAL
 * TemplateAction - Needs to be removed from admin, classes, migrations and DB
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief A list of Valid Actions (Business Processes) that we have templates for.
 * See TemplateMapping for information on their use.
 */
class TemplateAction extends Model
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
protected $fillable = ['action_name'];





}
