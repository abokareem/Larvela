<?php

Route::get( '/example','Example@Example');

#======================================================================
#
# REFACTOR ALL CALLS FROM Cart to be 1 call, PurchaseController@Purchase
# this needs to raise an order from the given cart->id and then,
# determine the payment chosen from the cart data
#
# 2018-12-06 New Checkout-Confirm-Purchase-Order
#
Route::post('/ajax/placeorder/{id}', 'Ajax\PlaceOrder@PlaceOrder');
#
#
# Purchase done - now save an order
#
Route::get( '/payment/etf/{id}', 'Order\OrderController@DelayedPurchase');
Route::post('/payment/etf/{id}', 'Order\OrderController@DelayedPurchase');
#
#
#
Route::post('/purchased', "Order\OrderController@Purchase");
#
# Paypal payment, could be instant or from item(s) in cart
# AJAX calls
#
Route::post('/instant/order/{id}',  'Order\OrderController@InstantPaypalPurchase');
Route::post('/cart/order/{id}',  'Order\OrderController@CartPaypalPurchase');
#
#
#
Route::post('/order/save/{id}',  'Order\OrderController@PurchaseMadeSaveOrder');
Route::post('/payment/pp/{id}',  'Order\OrderController@PaypalPurchase');
Route::post('/payment/cc/{id}',  'Order\OrderController@CCPurchase');
Route::post('/payment/cod/{id}', 'Order\OrderController@DelayedPurchase');
#
#
#
#
# Laravel routes files for a 5.3.x installation
#Notes: Test ing v5.7 show cluse causes issues... need to refactor this block.
#
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->get('auth/login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('auth/login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/auth/logout', function(){ Auth::logout(); return Redirect::to('/');});

// Registration Routes...
$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->post('register', 'Auth\RegisterController@register');

#
# @todo Move these to Ajax\DumpPaymentData.php
#
Route::post('/ajax/payment','AjaxController@dumpajax');
Route::get('/ajax/payment','AjaxController@dumpajax');

#
# 2016-12-10 needed for password reset return and post.
#
Route::get('/home','StoreFront\ShowStoreFront@ShowStoreFront');
Route::post('/home','StoreFront\ShowStoreFront@ShowStoreFront');

Route::post('/capture','CaptureController@CaptureForm');
Route::post('/cart/getcartdata','Ajax\CartData@GetCartData');
#
# 2018-09-18 Added new route
#
Route::post('/notify/outofstock','Ajax\OutOfStockNotify@OutOfStockNotify');
#
# Routes for Pages
#
#Route::get('/','StoreFrontController@ShowStoreFront');

Route::get('/','StoreFront\ShowStoreFront@ShowStoreFront');
Route::get('/attribute/{id}','StoreFront\ShopByAttribute@ShopByAttribute');
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
#
Route::post('/ajax/updatelocks/{id}','Ajax\UpdateLock@UpdateLock');
Route::post('/cart/updatelocks/{id}','Ajax\UpdateLock@UpdateLock');

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



#======================================================================
#
# Cart Basic Operations
#
Route::get('/addtocart/{id}', 'Cart\CartOperations@addItem');
Route::get('/removeItem/{productId}', 'Cart\CartOperations@removeItem');
Route::get('/cart/incqty/{cid}/{iid}','Cart\CartOperations@incCartQty');
Route::get('/cart/decqty/{cid}/{iid}','Cart\CartOperations@decCartQty');


#======================================================================
#
# From footer
#
Route::post('/subscribe','Subscription\SubscriptionController@AddNewSubscription');
#
# Called via links in email
#
Route::get( '/confirmed/{hash}', 'Subscription\SubscriptionController@ProcessConfirmed');
Route::get( '/unsubscribe/{hash}','Subscription\SubscriptionController@UnSubscribe');
#

#
# End of file
#
