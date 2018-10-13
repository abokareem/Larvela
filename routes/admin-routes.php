<?php

#
#  Routes for Admin operations
# Uses the CheckRole middle ware to ensure the user has the right role
#
# Route::get('/path',['uses'=>'Controller@function','roles'=>'ADMIN']);
# Route::get('/path',['uses'=>'Controller@function','roles'=>['ADMIN','USER'] ]);
#
#
#
Route::group(['middleware'=>['auth','roles']], function()
{
Route::get('/admin/mailrun/control','MailRunController@ShowPanel');
Route::post('/admin/mailrun/control','MailRunController@StartMailRun');


Route::get('/admin','AdminController@ShowDashboard');
Route::get('/dashboard','AdminController@ShowDashboard');
#
Route::get('/admin/orders','AdminOrderController@ShowCurrentOrders');
Route::get('/admin/order/view/{id}','AdminOrderController@ShowOrder');
#
#
# TO ADD TO PROD
Route::get('/admin/order/boitem/{id}','AdminOrderController@BackOrderAnItem');
#
Route::get('/admin/order/cancel/{id}','AdminOrderController@MarkOrderAsCancelled');
Route::post('/admin/order/dispatch/{id}','AdminOrderController@OrderDispatched');
Route::get('/admin/order/update/paid/{id}','AdminOrderController@MarkOrderPaid');
Route::get('/admin/order/update/unpaid/{id}','AdminOrderController@MarkOrderUnPaid');
Route::get('/admin/order/update/onhold/{id}','AdminOrderController@MarkOrderOnHold');
Route::get('/admin/order/update/waiting/{id}','AdminOrderController@MarkOrderAsWaiting');
Route::get('/admin/order/pdf/shopinvoice/{id}','AdminOrderController@DispayPDFShopInvoice');
Route::get('/admin/order/pdf/packingslip/{id}','AdminOrderController@DispayPDFPackingSlip');
#
#
Route::get('/admin/images', 'ImageManagement@Show');
Route::get('/admin/image/show/{pid}', 'ImageManagement@ShowByProduct');
Route::get('/admin/image/delete/{id}/{pid}',['uses'=>'BasicProductController@DeleteImage','roles'=>'root']);
#
#
Route::get( '/admin/settings','ConfigController@Show');
Route::get( '/admin/setting/edit/{id}','ConfigController@Edit');
Route::post('/admin/setting/update/{id}','ConfigController@Update');
Route::get( '/admin/setting/add','ConfigController@Add');
Route::post('/admin/setting/save','ConfigController@Save');
Route::get('/admin/setting/delete/{id}','ConfigController@Delete');
#
#
Route::get( '/admin/actions','TemplateActionController@Show');
Route::get( '/admin/action/add','TemplateActionController@Add');
Route::post('/admin/action/save','TemplateActionController@Save');
Route::get( '/admin/action/edit/{id}','TemplateActionController@Edit');
Route::post('/admin/action/update/{id}','TemplateActionController@Update');
Route::get( '/admin/action/delete/{id}','TemplateActionController@Delete');
#
#
Route::get( '/admin/templates','TemplateController@Show');
Route::get( '/admin/template/edit/{id}','TemplateController@Edit');
Route::get( '/admin/template/add', 'TemplateController@Add');
Route::post('/admin/template/save', 'TemplateController@Save');
Route::post('/admin/template/update/{id}', 'TemplateController@Update');
Route::post('/admin/template/delete/{id}', 'TemplateController@Delete');
#
#
Route::get('/admin/stores','AdminStoreController@ShowStoresPage');
Route::get('/admin/store/add','AdminStoreController@ShowAddStorePage');
Route::get('/admin/store/edit/{id}','AdminStoreController@ShowEditStorePage');
Route::get('/admin/store/addnew','AdminStoreController@ShowAddStorePage');
Route::post('/admin/store/save','AdminStoreController@SaveNewStore');
Route::post('/admin/store/update/{id}','AdminStoreController@UpdateStore');
#
#
Route::get('/admin/customers',['uses'=>'CustomerController@ShowCustomers','roles'=>['ADMIN','root']]);
Route::get('/admin/customer/edit/{id}','CustomerController@ShowEditCustomerPage');
Route::get('/admin/customer/addnew','CustomerController@ShowAddCustomerPage');
Route::post('/admin/customer/save','CustomerController@SaveNewCustomer');
Route::post('/admin/customer/update/{id}','CustomerController@UpdateCustomer');
#
#
Route::get('/admin/adverts',['uses'=>'AdvertController@ShowAdvertsPage','roles'=>'root']);
Route::get('/admin/advert/add',['uses'=>'AdvertController@ShowAddAdvertPage','roles'=>'root']);
Route::get('/admin/advert/edit/{id}',['uses'=>'AdvertController@ShowEditAdvertPage','roles'=>'root']);
Route::post('/admin/advert/save/{id}',['uses'=>'AdvertController@SaveNewAdvert','roles'=>'root']);
Route::post('/admin/advert/update/{id}',['uses'=>'AdvertController@UpdateAdvert','roles'=>'root']);
#
#
Route::get('/admin/categories','CategoryController@ShowCategoriesPage');
Route::get('/admin/category/addnew','CategoryController@ShowAddCategoryPage');
Route::get('/admin/category/edit/{id}','CategoryController@ShowEditCategoryPage');
Route::post('/admin/category/update/{id}','CategoryController@UpdateCategory');
Route::post('/admin/category/save','CategoryController@SaveNewCategory');
Route::post('/admin/category/delete/{id}','CategoryController@DeleteCategory');
Route::post('/admin/category/deletecat/{id}','CategoryController@DoDeleteCategory');
#
#
#
Route::get('/admin/products/select/type','AdminProductController@SelectType');
Route::get('/admin/select/type/{id}','AdminProductController@RouteToPage');


#
# 2018-09-22 replace these with ProductFactory call in ProductController.
#
##Route::post('/admin/product/save-bp','BasicProductController@Save');
##Route::post('/admin/product/save-pp','ParentProductController@Save');
##Route::post('/admin/product/save-lv','VirtualProductController@SaveLimitedVirtual');
##Route::post('/admin/product/save-uv','VirtualProductController@SaveUnLimitedVirtual');

#
#
#
Route::get('/admin/products','AdminProductController@ShowProductsPage');
Route::get('/admin/product/addnew','AdminProductController@ShowAddProductPage');
Route::get('/admin/product/edit/{id}','AdminProductController@ShowEditProductPage');
#
# 2018-09-22 use factory object in product controller to route to product
#
Route::post('/admin/product/save','ProductController@Save');
Route::post('/admin/product/update/{id}','ProductController@Update');
Route::post('/admin/product/delete/{id}','ProductController@Delete');
#
#
#
Route::get( '/admin/product/copy/{id}','AdminProductController@ShowCopyProductPage');
Route::post('/admin/product/copy/{id}','AdminProductController@CopyProductPage');
#
#
#
Route::get('/admin/attributes','BasicProductController@ShowAttributesPage');
#
#
Route::get('/admin/producttypes','ProductTypeController@Show');
Route::get('/admin/producttype/edit/{id}','ProductTypeController@Edit');
Route::get('/admin/producttype/addnew','ProductTypeController@Add');
Route::post('/admin/producttype/save','ProductTypeController@Save');
Route::post('/admin/producttype/update/{id}','ProductTypeController@Update');
Route::post('/admin/producttype/delete/{id}','ProductTypeController@Delete');
#
#
Route::get('/admin/seo','SEOController@ShowSEOList');
Route::get('/admin/seo/addnew','SEOController@ShowAddSEO');
Route::get('/admin/seo/edit/{id}','SEOController@ShowEditPage');
Route::post('/admin/seo/save','SEOController@SaveNewSEO');
Route::post('/admin/seo/update/{id}','SEOController@UpdateSEO');
	
}); # end of route group

