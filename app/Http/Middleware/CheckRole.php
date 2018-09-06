<?php
/**
 * \class	CheckRole
 *
 *
 *
 *
 */
namespace App\Http\Middleware;

/**
 * \class CheckRole
 */

use Closure;

/**
 * \brief Role checker middleware class. Used in the route to limit access to authorised users who have this role.
 */
class CheckRole
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
		// Get the required roles from the route
		$roles = $this->getRequiredRole($request->route());
		// Check if a role is required for the route, and
		// if so, ensure that the user has that role.
		if($request->user()->hasRole($roles) || !$roles)
		{
			return $next($request);
		}
		return response([
			'error' => [
				'code' => 'INSUFFICIENT_ROLE',
				'description' => 'You are not authorized to access this resource.'
			]
		], 401);

	}


	/**
	 * Return the attribute "roles" if its present or null
	 *
	 * @param	Request	$route
	 * @return	mixed	array or null
	 */
	private function getRequiredRole($route)
	{
		$actions = $route->getAction();
		return isset($actions['roles']) ? $actions['roles'] : null;
	}

}
