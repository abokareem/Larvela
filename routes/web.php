<?php

require('admin-routes.php');
require('store-routes.php');
if( env("APP_ENV", "NOT_DEFINED") == "DEV")
{
	require('test-routes.php');
}

Auth::routes();

#
# Catch ALL page router
#
Route::any( '{catchall}', 'SupportController@user_defined_page');
