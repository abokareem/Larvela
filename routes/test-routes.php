<?php
#
# Routes used in testing - do not push to PROD
#
Route::group(['middleware'=>['auth','roles']], function()
{
Route::get('/test/category/image','TestController@test_category_image');
});




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


Route::get('/test/url', 'TestController@test_url');
Route::get('/test/footer', 'TestController@test_footer');
Route::get('/test/header', 'TestController@test_header');



Route::get('/admin/search', 'SearchController@Search');
Route::get('/mailrun', 'Test\TestMail@mailrun');
