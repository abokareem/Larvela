<?php
#
# Routes used in testing - do not push to PROD
#
Route::group(['middleware'=>['auth','roles']], function()
{
Route::get('/test/category/image','TestController@test_category_image');
});

Route::get('/test/interfaces','TestController@test_interfaces');




Route::get('/test/cart/show/{id}','CartController@test_cart_show');


Route::get('/test/product/packs','TestController@test_product_packs');


Route::get('/test/product/show/{id}','TestController@test_product_show');

Route::get('/test/dispatch/email','TestController@test_dispatch_email');
#
#
#
Route::get('/test/stock/outofstock','TestController@test_stock_outofstock');
Route::get('/test/stock/backinstock','TestController@test_stock_backinstock');
#
#
#
Route::get('/test/login/failed','TestController@test_login_failed');



Route::get('/test/cart/abandoned/{days}','TestController@test_cart_abandoned');
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

#
# TEST code to do a bulk update - started but not finished.
#
Route::get('/bulkupdate-20170910','BasicProductController@BulkUpdate');
Route::post('/ajax/update/{id}','AjaxController@dumpajax');

