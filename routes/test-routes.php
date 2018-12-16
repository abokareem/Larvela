<?php
#
# Routes used in testing - do not push to PROD
#
Route::group(['middleware'=>['auth','roles']], function()
{
Route::get('/test/category/image','Test\TestController@test_category_image');
});

Route::get('/test/cart/ajax','Test\TestCart@test_cart_ajax');

Route::get('/nav',function()
{
	
	return view('test-topbar',['store'=>app('store')]); 
});

Route::get('/test/pagination/options','Test\TestController@test_pagination_options');


Route::get('/test/show/cart/message','Test\TestEvents@test_show_cart_message');
Route::get('/test/add/to/cart/message','Test\TestEvents@test_add_to_cart_message');
Route::get('/test/place/order/message','Test\TestEvents@test_place_order_message');




Route::get('/test/filter/get','Test\TestController@test_filter_get');
Route::get('/test/filter/products','Test\TestController@test_filter_products');


Route::get('/test/product/packs','TestController@test_product_packs');
Route::get('/test/product/show/{id}','TestController@test_product_show');

Route::get('/test/dispatch/email','TestController@test_dispatch_email');
#
#
#
Route::get('/test/stock/outofstock','Test\TestMail@test_stock_outofstock');
Route::get('/test/stock/backinstock','Test\TestMail@test_stock_backinstock');
#
#
#
Route::get('/test/login/failed','Test\TestMail@test_login_failed');



Route::get('/test/cart/data','Test\TestCart@test_cart_data');
Route::get('/test/cart/show/{id}','Test\TestCart@test_cart_show');
Route::get('/test/cart/abandoned/{days}','Test\TestCart@test_cart_abandoned');
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
Route::get('/test/order/paid','Test\TestMail@test_order_paid');
Route::get('/test/order/placed','Test\TestMail@test_order_placed');
Route::get('/test/order/unpaid','Test\TestMail@test_order_unpaid');
Route::get('/test/order/onhold','Test\TestMail@test_order_onhold');
Route::get('/test/order/pending','Test\TestMail@test_order_pending');
Route::get('/test/order/cancelled','Test\TestMail@test_order_cancelled');
Route::get('/test/order/dispatched','Test\TestMail@test_order_dispatched');

#
#
#
Route::get('/test/error/subscription','Test\TestRoutes@test_error_subscriptionerror');
Route::get('/test/error/nostock','Test\TestRoutes@test_error_cartitemoutofstock');
Route::get('/test/error/noproduct','Test\TestRoutes@test_error_nomatchingproducts');
Route::get('/test/error/carttimeout','Test\TestRoutes@test_error_carttimeout');
Route::get('/test/error/noroute','Test\TestRoutes@test_error_noroute');



Route::get('/test/url', 'TestController@test_url');
Route::get('/test/footer', 'TestController@test_footer');
Route::get('/test/header', 'TestController@test_header');



Route::get('/admin/search', 'SearchController@Search');
Route::get('/mailrun', 'Test\TestMail@mailrun');
