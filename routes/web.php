<?php

require('admin-routes.php');
require('store-routes.php');
if( env("APP_ENV", "NOT_DEFINED") == "DEV")
{
	require('test-routes.php');
}
$install_files = base_path('routes/install-routes.php');
if(file_exists($install_files))
{
	require($install_files);
}

Auth::routes();

#
# Catch ALL page router
#
Route::any( '{catchall}', 'SupportController@user_defined_page');
