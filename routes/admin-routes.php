<?php

#
#  Routes for Admin operations
# Uses the CheckRole middle ware to ensure the user has the right role
#
# Route::get('/path',['uses'=>'Controller@function','roles'=>'ADMIN']);
# Route::get('/path',['uses'=>'Controller@function','roles'=>['ADMIN','USER'] ]);
#
# 2018
#
Route::group(['middleware'=>['auth','roles:ADMIN,ORDERS']], function()
{
Route::get('/admin/orders','Admin\AdminOrderController@ShowCurrentOrders');
Route::get('/admin/order/view/{id}','Admin\AdminOrderController@ShowOrder');
#
# TO ADD TO PROD
Route::get('/admin/order/boitem/{id}','Admin\AdminOrderController@BackOrderAnItem');
#
Route::get('/admin/order/cancel/{id}','Admin\AdminOrderController@MarkOrderAsCancelled');
Route::post('/admin/order/dispatch/{id}','Admin\AdminOrderController@OrderDispatched');
Route::get('/admin/order/update/paid/{id}','Admin\AdminOrderController@MarkOrderPaid');
Route::get('/admin/order/update/unpaid/{id}','Admin\AdminOrderController@MarkOrderUnPaid');
Route::get('/admin/order/update/onhold/{id}','Admin\AdminOrderController@MarkOrderOnHold');
Route::get('/admin/order/update/waiting/{id}','Admin\AdminOrderController@MarkOrderAsWaiting');
Route::get('/admin/order/pdf/shopinvoice/{id}','Admin\AdminOrderController@DispayPDFShopInvoice');
Route::get('/admin/order/pdf/packingslip/{id}','Admin\AdminOrderController@DispayPDFPackingSlip');
});


Route::group(['middleware'=>['auth','roles:ADMIN,PRODUCTADMIN']], function()
{
#
Route::get('/admin/products/select/type','Admin\AdminProductController@SelectType');
Route::get('/admin/select/type/{id}','Admin\AdminProductController@RouteToPage');
#
#
#
Route::get('/admin/products','Admin\AdminProductController@ShowProductsPage');
Route::get('/admin/product/addnew','Admin\AdminProductController@ShowAddProductPage');
Route::get('/admin/product/edit/{id}','Admin\AdminProductController@ShowEditProductPage');
#
# 2018-09-22 use factory object in product controller to route to product
#
Route::post('/admin/product/save','Product\ProductController@Save');
Route::post('/admin/product/update/{id}','Product\ProductController@Update');
Route::post('/admin/product/delete/{id}','Product\ProductController@Delete');
Route::get( '/admin/product/copy/{id}','Admin\AdminProductController@ShowCopyProductPage');
Route::post('/admin/product/copy/{id}','Admin\AdminProductController@CopyProductPage');
});



Route::group(['middleware'=>['auth','roles:ADMIN']], function()
{
Route::get('/admin/mailrun/control','MailRunController@ShowPanel');
Route::post('/admin/mailrun/control','MailRunController@StartMailRun');


Route::get('/admin','Admin\AdminController@ShowDashboard');
Route::get('/dashboard','Admin\AdminController@ShowDashboard');
#
#
Route::get('/admin/images', 'ImageManagement@Show');
Route::get('/admin/image/show/{pid}', 'ImageManagement@ShowByProduct');
Route::get('/admin/image/delete/{id}/{pid}',['uses'=>'Product\BasicProductController@DeleteImage','roles'=>'root']);
#
#
Route::get( '/admin/settings','ConfigController@Show');
Route::get( '/admin/setting/edit/{id}','ConfigController@Edit');
Route::post('/admin/setting/update/{id}','ConfigController@Update');
Route::get( '/admin/setting/add','ConfigController@Add');
Route::post('/admin/setting/save','ConfigController@Save');
Route::get('/admin/setting/delete/{id}','ConfigController@Delete');


Route::get('/admin/stores','Admin\AdminStoreController@ShowStoresPage');
Route::get('/admin/store/add','Admin\AdminStoreController@ShowAddStorePage');
Route::get('/admin/store/edit/{id}','Admin\AdminStoreController@ShowEditStorePage');
Route::get('/admin/store/addnew','Admin\AdminStoreController@ShowAddStorePage');
Route::post('/admin/store/save','Admin\AdminStoreController@SaveNewStore');
Route::post('/admin/store/update/{id}','Admin\AdminStoreController@UpdateStore');


Route::get('/admin/customers',['uses'=>'CustomerController@ShowCustomers','roles'=>['ADMIN','root']]);
Route::get('/admin/customer/edit/{id}','CustomerController@ShowEditCustomerPage');
Route::get('/admin/customer/addnew','CustomerController@ShowAddCustomerPage');
Route::post('/admin/customer/save','CustomerController@SaveNewCustomer');
Route::post('/admin/customer/update/{id}','CustomerController@UpdateCustomer');


Route::get('/admin/adverts',['uses'=>'AdvertController@ShowAdvertsPage','roles'=>'root']);
Route::get('/admin/advert/add',['uses'=>'AdvertController@ShowAddAdvertPage','roles'=>'root']);
Route::get('/admin/advert/edit/{id}',['uses'=>'AdvertController@ShowEditAdvertPage','roles'=>'root']);
Route::post('/admin/advert/save/{id}',['uses'=>'AdvertController@SaveNewAdvert','roles'=>'root']);
Route::post('/admin/advert/update/{id}',['uses'=>'AdvertController@UpdateAdvert','roles'=>'root']);


Route::get('/admin/categories','CategoryController@ShowCategoriesPage');
Route::get('/admin/category/addnew','CategoryController@ShowAddCategoryPage');
Route::get('/admin/category/edit/{id}','CategoryController@ShowEditCategoryPage');
Route::post('/admin/category/update/{id}','CategoryController@UpdateCategory');
Route::post('/admin/category/save','CategoryController@SaveNewCategory');
Route::post('/admin/category/delete/{id}','CategoryController@DeleteCategory');
Route::post('/admin/category/deletecat/{id}','CategoryController@DoDeleteCategory');


Route::get('/admin/producttypes','Product\ProductTypeController@Show');
Route::get('/admin/producttype/edit/{id}','Product\ProductTypeController@Edit');
Route::get('/admin/producttype/addnew','Product\ProductTypeController@Add');
Route::post('/admin/producttype/save','Product\ProductTypeController@Save');
Route::post('/admin/producttype/update/{id}','Product\ProductTypeController@Update');
Route::post('/admin/producttype/delete/{id}','Product\ProductTypeController@Delete');
#
#
Route::get('/admin/seo','SEOController@ShowSEOList');
Route::get('/admin/seo/addnew','SEOController@ShowAddSEO');
Route::get('/admin/seo/edit/{id}','SEOController@ShowEditPage');
Route::post('/admin/seo/save','SEOController@SaveNewSEO');
Route::post('/admin/seo/update/{id}','SEOController@UpdateSEO');
	

Route::get('/admin/attributes','Admin\AttributesController@ShowAttributesPage');
Route::get('/admin/attribute/addnew','Admin\AttributesController@AddNew');
Route::get('/admin/attribute/edit/{id}','Admin\AttributesController@Edit');
Route::post('/admin/attribute/delete','Admin\AttributesController@Delete');
Route::post('/admin/attribute/save','Admin\AttributesController@Save');
Route::post('/admin/attribute/update/{id}','Admin\AttributesController@Update');


Route::get('/admin/subscriptions','Subscription\ShowSubscriptions@ShowSubscription');

#
# Templates are now obsolete thanks to Mailable interface
#
Route::get( '/admin/actions','TemplateActionController@Show');
Route::get( '/admin/action/add','TemplateActionController@Add');
Route::post('/admin/action/save','TemplateActionController@Save');
Route::get( '/admin/action/edit/{id}','TemplateActionController@Edit');
Route::post('/admin/action/update/{id}','TemplateActionController@Update');
Route::get( '/admin/action/delete/{id}','TemplateActionController@Delete');


Route::get( '/admin/templates','TemplateController@Show');
Route::get( '/admin/template/edit/{id}','TemplateController@Edit');
Route::get( '/admin/template/add', 'TemplateController@Add');
Route::post('/admin/template/save', 'TemplateController@Save');
Route::post('/admin/template/update/{id}', 'TemplateController@Update');
Route::post('/admin/template/delete/{id}', 'TemplateController@Delete');

}); # end of route group

