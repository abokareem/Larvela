<?php
/**
 * \class User
 * @date 2016-12-09
 */
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * \brief User model has code to process Role relationships and authentication.
 */
class User extends Authenticatable
{
use Notifiable;


/**
 * The attributes that are mass assignable.
 *
 * @var array $fillable
 */
protected $fillable = ['name','email','password','role_id'];


/**
* The attributes that should be hidden for arrays.
*
* @var array $hidden
*/
protected $hidden = [ 'password', 'remember_token', ];



	/**
	 * Map Role to User, each user has 1 role.
	 *
	 * @return	mixed
	 */
	public function role()
	{
		return $this->hasOne('App\Models\Role', 'id', 'role_id');
	}


	/**
	 * Map Role to User, each user has 1 role.
	 *
	 *
	 * @param	array	$roles
	 * @return	boolean
	 */
	public function hasRole($roles)
	{
		$this->have_role = $this->getUserRole();
		
		// Check if the user is a root account
		if($this->have_role->name == 'root')
		{
			return true;
		}
		if(is_array($roles))
		{
			foreach($roles as $need_role)
			{
				if($this->checkIfUserHasRole($need_role))
				{
					return true;
				}
			}
		}
		else
		{
			return $this->checkIfUserHasRole($roles);
		}
		return false;
	}


	/**
	 *
	 *
	 * @return	array
	 */
	private function getUserRole()
	{
		return $this->role()->getResults();
	}



	/**
	 *
	 *
	 * @return	boolean
	 */
	private function checkIfUserHasRole($need_role)
	{
		return (strtolower($need_role)==strtolower($this->have_role->name)) ? true : false;
	}
}
