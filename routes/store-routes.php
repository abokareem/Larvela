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

Route::post('/ajax/payment','AjaxController@dumpajax');
Route::get('/ajax/payment','AjaxController@dumpajax');

#
# 2016-12-10 needed for password reset return and post.
#
Route::get('/home','StoreFrontController@ShowStoreFront');
Route::post('/home','StoreFrontController@ShowStoreFront');

Route::post('/capture','CaptureController@CaptureForm');
Route::post('/cart/getcartdata','Ajax\CartData@GetCartData');
#
# 2018-09-18 Added new route
#
Route::post('/notify/outofstock','Ajax\OutOfStockNotify@OutOfStockNotify');
#
# Routes for Pages
#
Route::get('/','StoreFrontController@ShowStoreFront');
Route::get('/attribute/{id}','StoreFrontController@ShopByAttribute');
#
# 2018-09-12 refactored this route from StoreFrontController.
#
Route::get('/product/{id}','Product\ProductPageController@ShowProductPage');
Route::get('/category/{id}','CategoryPageController@ShowStoreCategory');

#
# CMS/SEO pages
#
Route::get('/about',  'SupportController@about'); 
Route::get('/tandc',  'SupportController@tandc');
Route::get('/support','SupportController@support');
Route::get('/privacy','SupportController@privacy');
Route::get('/contact','SupportController@contact');

#
#
#
#
# Cart/account related
#
Route::get('/cart', 'Cart\CartController@ShowCart');

#
# 2018-09-19 Split from CartController
#
Route::get('/checkout','Cart\CheckoutController@Checkout');
Route::get('/shipping','Cart\CheckoutController@Shipping');
Route::get('/confirm', 'Cart\CartConfirm@Confirm');
Route::post('/confirm','Cart\CartConfirm@Confirm');
Route::get('/cart/shipping','Cart\CheckoutController@ShowShipping');

#
# Moved to Cart\CartLocking
#
Route::post('/ajax/updatelocks/{id}','Cart\CartLocking@UpdateLocks');
Route::post('/cart/updatelocks/{id}','Cart\CartLocking@UpdateLocks');

#
# Error Trapping
#
Route::get('/cart/error/cart-timeout','Cart\CartController@CartTimeoutError');

#
#
#
Route::get('/signup', 'StoreController@ShowSignUpForm');
Route::get('/myaccount', 'StoreController@ShowMyAccount');
Route::post('/myaccount/update/{id}', 'CustomerController@UpdateMyAccount');

#
# Cart Logic
#
Route::get('/addtocart/{id}', 'Cart\CartOperations@addItem');
Route::get('/removeItem/{productId}', 'Cart\CartOperations@removeItem');
Route::get('/cart/incqty/{cid}/{iid}','Cart\CartOperations@incCartQty');
Route::get('/cart/decqty/{cid}/{iid}','Cart\CartOperations@decCartQty');

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
