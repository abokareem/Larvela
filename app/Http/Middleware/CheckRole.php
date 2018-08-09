<?php namespace App\Http\Middleware;

/**
 * \class CheckRole
 */
// First copy this file into your middleware directoy

use Closure;

/**
 * \brief Third party Role checker middleware class
 */
class CheckRole{

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
		$roles = $this->getRequiredRoleForRoute($request->route());

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
	private function getRequiredRoleForRoute($route)
	{
		$actions = $route->getAction();
		return isset($actions['roles']) ? $actions['roles'] : null;
	}

}
