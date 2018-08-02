<?php

#
# Purchase done - now save an order
#
Route::get( '/payment/etf/{id}', 'OrderController@DelayedPurchase');
Route::post('/payment/etf/{id}', 'OrderController@DelayedPurchase');
Route::post('/purchased', "OrderController@Purchase");
#
# Paypal payment, could be instant or from item(s) in cart
# AJAX calls
#
Route::post('/instant/order/{id}',  'OrderController@InstantPaypalPurchase');
Route::post('/cart/order/{id}',  'OrderController@CartPaypalPurchase');
#
#
#
Route::post('/order/save/{id}',  'OrderController@PurchaseMadeSaveOrder');
Route::post('/payment/pp/{id}',  'OrderController@PaypalPurchase');
Route::post('/payment/cc/{id}',  'OrderController@CCPurchase');
Route::post('/payment/cod/{id}', 'OrderController@DelayedPurchase');
#
#
#
/**
 * Laravel routes files for a 5.3.x installation
 */
 // Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->get('auth/login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('auth/login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
#$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
#$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
#$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
#$this->post('password/reset', 'Auth\ResetPasswordController@reset');


Route::post('/ajax/payment','AjaxController@dumpajax');
Route::get('/ajax/payment','AjaxController@dumpajax');

#
# 2016-12-10 needed for password reset return and post.
#
Route::get('/home','StoreFrontController@ShowStoreFront');
Route::post('/home','StoreFrontController@ShowStoreFront');

Route::post('/capture','StoreFrontController@CaptureForm');
Route::post('/notify/outofstock','StoreFrontController@OutOfStockNotify');
Route::post('/cart/getcartdata','StoreFrontController@GetCartData');
#
# Routes for Pages
#
Route::get('/','StoreFrontController@ShowStoreFront');
Route::get('/category/{id}','StoreFrontController@ShowStoreCategory');
Route::get('/product/{id}','StoreFrontController@ShowProductPage');
Route::get('/attribute/{id}','StoreFrontController@ShopByAttribute');

#
# CMS/SEO pages
#
Route::get('/about',  'SupportController@about'); 
Route::get('/tandc',  'SupportController@tandc');
Route::get('/support','SupportController@support');
Route::get('/privacy','SupportController@privacy');
Route::get('/contact','SupportController@contact');

#
# Cart/account related
#
Route::get('/cart', 'CartController@ShowCart');
Route::get('/cart/incqty/{cid}/{iid}','CartController@incCartQty');
Route::get('/cart/decqty/{cid}/{iid}','CartController@decCartQty');
Route::get('/cart/shipping','CartController@ShowShipping');
Route::get('/cart/checkout','CartController@Checkout');
Route::get('/signup', 'StoreController@ShowSignUpForm');
#
#
#
Route::post('/ajax/updatelocks/{id}','CartController@UpdateLocks');
Route::post('/cart/updatelocks/{id}','CartController@UpdateLocks');
#
# Error Trapping
#
Route::get('/cart/error/cart-timeout','CartController@CartTimeoutError');


Route::post('/myaccount/update/{id}', 'CustomerController@UpdateMyAccount');
Route::get('/myaccount', 'StoreController@ShowMyAccount');
#
# 2016-09-05 Cart Logic
#
Route::get('/addtocart/{id}', 'CartController@addItem');
Route::get('/removeItem/{productId}', 'CartController@removeItem');
Route::get('/shipping','CartController@ShowShipping');
Route::post('/confirm','CartController@Confirm');

#
# From footer
#
Route::post('/subscribe','SubscriptionController@AddNewSubscription');
#
# Called via links in email
#
Route::get( '/confirmed/{hash}', 'SubscriptionController@ProcessConfirmed');
Route::get( '/unsubscribe/{hash}','SubscriptionController@UnSubscribe');

Route::get('/auth/logout', function(){ Auth::logout(); return Redirect::to('/');});
#
# End of file
#
