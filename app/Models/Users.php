<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
public $timestamps = false;

protected $id;
protected $name;
protected $email;
protected $password;
protected $remember_token;
protected $created_at;
protected $updated_at;
protected $role_id;



protected $fillable = ['name','email','password', 'role_id',
				'remember_token','created_at','updated_at'];



	/**
	 *
	 *
	 * @return	mixed
	 */
	public function getArray()
	{
		return array(
			'id'=>$this->id,
			'name'=>$this->name,
			'email'=>$this->email,
			'password'=>$this->password,
			'remember_token'=>$this->remember_token,
			'created_at'=>$this->created_at,
			'updated_at'=>$this->updated_at,
			'role_id'=>$this->role_id
			);
	}
	

	/**
	 *
	 *
	 * @return	mixed
	 */
	public function InsertUser($d)
	{
		$this->name   = $d['name'];
		$this->email  = $d['email'];
		$this->password = $d['password'];
		$this->role_id = $d['role_id'];

		try
		{
		$this->id = \DB::table('users')->insertGetId( array(
			'name'   =>$d['name'],
			'email'  =>$d['email'],
			'password' =>$d['password'],
			'role_id' =>$d['role_id']
			));
		return $this->id;
		}
		catch(\Illuminate\Database\QueryException $e)
		{
			return 0;
		}
	}
}
