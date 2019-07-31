<?php
/**
 * \class	CheckAdmin 
 * \date	2019-07-25
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \version	1.0.0
 * 
 * Usage: 
 * $this->middleware(CheckAdmin::class);
 */
namespace App\Http\Middleware;

use Auth;
use Closure;
use Session;
use App\User;


class CheckAdmin
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$UID = Auth::user()->id;
		$user = User::find($UID);
		if($user->role_id==1)
			return $next($request);
		else
			return redirect('/');
	}
}
