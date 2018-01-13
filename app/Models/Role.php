<?php
/**
 * \class	Role
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-07-29
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Basic model for the role mapping table.
 *
 * Used to map the user to a role.
 */
class Role extends Model
{

/**
 * name of table
 * @var string $table
 */
protected $table = 'role';


	/**
	 * Return a Collection of rows joined with the User table
	 * using the role_id.
	 *
	 * @return	mixed
	 */
    public function users()
    {
        return $this->hasMany('App\User', 'role_id', 'id');
    }


	/**
	 * Given a string role name, return all rows that match
	 *
	 * @param	string	$r	the role name
	 * @return	mixed
	 */
	public function getByRole($r)
	{
		return \DB::table('role')->where(['name'=>$r])->get();
	}

}
