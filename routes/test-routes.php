<?php
#
# Routes used in testing - do not push to PROD
#

Route::get('/test/product/show/{id}','TestController@test_product_show');

#
#
#
Route::get('/test/stock/outofstock','TestController@test_stock_outofstock');
Route::get('/test/stock/backinstock','TestController@test_stock_backinstock');
#
#
#
Route::get('/test/login/failed','TestController@test_login_failed');



Route::get('/test/cart/abandoned','TestController@test_cart_abandoned');
#
# 2018-07-17 - New route formats /test/<section>/<method>
#
Route::get('/test/subscription/confirm','TestController@test_subscription_confirm');
Route::get('/test/subscription/confirmed','TestController@test_subscription_confirmed');
Route::get('/test/subscription/sendwelcome','TestController@test_subscription_sendwelcome');
Route::get('/test/subscription/finalrequest','TestController@test_subscription_finalrequest');
Route::get('/test/subscription/resend','TestController@test_subscription_resend');

#
# Orders
#
Route::get('/test/order/cancelled','TestController@test_order_cancelled');
Route::get('/test/order/dispatched','TestController@test_order_dispatched');
Route::get('/test/order/onhold','TestController@test_order_onhold');
Route::get('/test/order/paid','TestController@test_order_paid');
Route::get('/test/order/pending','TestController@test_order_pending');
Route::get('/test/order/placed','TestController@test_order_placed');
Route::get('/test/order/unpaid','TestController@test_order_unpaid');


Route::get('/test/url', 'TestController@test_url');
Route::get('/test/footer', 'TestController@test_footer');
Route::get('/test/header', 'TestController@test_header');



Route::get('/admin/search', 'SearchController@Search');
Route::get('/mailrun', 'TestController@mailrun');
Route::get('/themeinfo', function()  { return View::make('themeinfo'); });

/**
 * Laravel routes files for a 5.2.x installation
  *
  Route::get('/we', 'StoreFrontController@testemail');
  Route::get('/cs', 'StoreFrontController@testemail');
  Route::get('/sc', 'StoreFrontController@testemail');
  Route::get('/od', 'StoreFrontController@testemail');
  Route::get('/op', 'StoreFrontController@testemail');
  Route::get('/delete/{id}', 'StoreFrontController@testimagedelete');
  Route::get('/mr', 'AdminController@MailRun');
*/


#
# TEST code to do a bulk update - started but not finished.
#
Route::get('/bulkupdate-20170910','BasicProductController@BulkUpdate');
Route::post('/ajax/update/{id}','AjaxController@dumpajax');

