<?php
/**
 * \class	User
 * \date	2016-12-09
 * \version	1.0.2
 *
 *
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
	 * Check if the user is an admin/root user, return true
	 *
	 * Called from Middleware and passed in an array of suitable roles.
	 * Get a Collection of Role objects for the user and iterate through them.
	 * - Otherwise check if user has the role assigned.
	 *
	 * @param	array	$roles
	 * @return	boolean
	 */
	public function hasRole($roles)
	{
		if($this->role_id == 1) return true;

		$assigned_roles = $this->roles;
		foreach($assigned_roles as $ar)
		{
			if(is_array($roles))
			{
				foreach($roles as $role_needed)
				{
					if(strtoupper($role_needed) == strtoupper($ar->role_name))
					{
						return true;
					}
				}
			}
			else
			{
				if($ar->role_name == strtoupper($roles))
				{
					return true;
				}
			}
		}
		return false;
	}



	/**
	 * User can have lots of roles, this returns a collection of Role objects the user has.
	 *
	 * @return	mixed
	 */
	public function roles()
	{
		return $this->belongsToMany('App\Models\Role', 'user_role','user_id','role_id');
	}
}
