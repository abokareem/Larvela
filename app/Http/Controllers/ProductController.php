<?php
/**
 * \class	ProductController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-18
 *
 *
 *
 * [CC]
 */
namespace App\Http\Controllers;


use Input;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ProductRequest;
use Illuminate\Contracts\Bus\Dispatcher;


use App\Helpers\StoreHelper;
use App\Services\ProductService;


use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\BackInStock;
use App\Jobs\ResizeImages;


use App\Models\Store;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\CategoryProduct;

use App\Models\Attribute;
use App\Models\ProdImageMap;
use App\Models\Notification;


use App\Traits\Logger;


/**
 * \brief MVC Controller to Handle the Product Administration functions.
 *
 * {INFO_2018-03-01} Removed all code except copy and product add 
 */
class ProductController extends Controller
{
use Logger;


	/**
	 * Open log file
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->setFileName("store-admin");
		$this->LogStart();
		$this->LogMsg("CLASS:BasicProductController");
	}
	
	/**
	 * Close log file
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS:BasicProductController");
		$this->LogEnd();
	}





	/**
	 * CHANGE TO SHOW PRODUCT TYPES, Then ROute to correct view
	 *
	 *
	 *
	 *
	 * Call the view to present the "Add New" Product page
	 * was "/admin/product/addnew"
	 * GET ROUTE: /admin/select/type
	 *
	 * @return mixed - view object
	 */
	public function SelectType()
	{
		$this->LogFunction("SelectType()");
		$store = app('store');
		$stores = Store::all();
		$product_types = ProductType::all();
		return view('Admin.Products.selecttype',[
			'product_types'=>$product_types,
			'stores'=>$stores
			]);
	}


	/**
	 *
	 *
	 * POST ROUTE: /admin/select/type/{id}
	 *
	 * @return mixed 
	 */
	public function RouteToPage($id)
	{
		$this->LogFunction("RouteToPage()");
		$product_types = ProductType::all();
		$selected_type = "";
		$product_type = ProductType::where('product_type',"Basic Product")->first();
		foreach($product_types as $pt)
		{
			if($id == $pt->id)
			{
				$product_type = $pt;
				$selected_type=$pt->product_type;
				break;
			}
		}

		$stores = Store::all();
		$categories = Category::all();

		switch($selected_type)
		{
			case "Parent Product":
				return view('Admin.Products.add_parent',[
					'product_type'=>$product_type,
					'stores'=>$stores,
					'categories'=>$categories ]);
				break;
			case "Virtual Virtual Product (Limited)":
				return view('Admin.Products.add_virtual',[
					'product_type'=>$product_type,
					'stores'=>$stores,
					'categories'=>$categories ]);
				break;
			case "Virtual Product (Unlimited)":
				return view('Admin.Products.add_unlimited_virtual',[
					'product_type'=>$product_type,
					'stores'=>$stores,
					'categories'=>$categories ]);
				break;
			default:
				return view('Admin.Products.add_basic',[
					'product_type'=>$product_type,
					'stores'=>$stores,
					'categories'=>$categories ]);
				break;
		}
	}




	/**
	 * Present a new page which allows SKU entry, then post back.
	 *
	 * {FIX_2017-10-24} Refactored product fetch using eloquent call in ShowCopyProductPage()
	 *
	 * GET ROUTE: /admin/product/copy/{id}
	 *
	 * @param	integer	$id		Product to copy
	 * @return	mixed
	 */
	public function ShowCopyProductPage($id)
	{
		$this->LogFunction("ShowCopyProductPage()");
		$product = Product::find($id);
		return view('Admin.Products.copy',['product'=>$product]);
	}



	/**
	 * Using the new SKU, read the existing product using the ID, insert a new product with the new SKU.
	 * Dont copy the images.
	 * Dont match the categories.
	 *
	 * POST ROUTE: /admin/product/copy/{id}
	 *
	 * @param	integer	$id		Product to use as a tempalte to copy from.
	 * @return	mixed
	 */
	public function CopyProductPage(Request $request, $id)
	{
		$this->LogFunction("CopyProductPage()");
		$this->LogMsg("Source Product ID [".$id."]");

		$base_product = Product::find($id);
		$duplicate_count  = Product::where('prod_sku',$request['prod_sku'])->count();
		if($duplicate_count == 0)
		{
			$base_product['prod_sku'] = $request['prod_sku'];
			$prod_categories = CategoryProduct::where('product_id',$id)->get();
			foreach($prod_categories as $pc)
			{
				$this->LogMsg("Product is assigned to category [".$pc->category_id."]");
			}

			$data = $base_product->toArray();
			$this->LogMsg("New Product".print_r($data, true));
			$new_pid = ProductService::insertArray($data);
			$this->LogMsg("Product [".$id."] copied, new Product ID [".$new_pid."]");
			$saved_categories = array();
			$this->LogMsg("Checking for duplicates?");
			foreach($prod_categories as $pc)
			{
				if(!in_array($pc->category_id, $saved_categories))
				{
					$this->LogMsg("Insert Cat [".$pc->category_id."]   Prod [".$new_pid."]");
					$o = new CategoryProduct;
					$o->category_id = $pc->category_id;
					$o->product_id  = $new_pid;
					$o->save();
					array_push($saved_categories, $pc->category_id);
				}
				else
				{
					$this->LogMsg("Duplicate category found [".$pc->category_id."]");
				}
			}
		}
		else
		{
			\Session::flash('flash_error','ERROR - Product SKU alreay in Database!');
		}
		return $this->ShowProductsPage($request);
	}







}
