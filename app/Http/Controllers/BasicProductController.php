<?php
/**
 * \class	BasicProductController
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
 * {INFO_2017-09-11} Added support for prod_has_free_shipping
 * {INFO_2017-10-26} Added Support for BackInStock Job dispatch
 */
class BasicProductController extends Controller
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
	 * Return an administration view listing the selected products.
	 *
	 * GET ROUTE: /admin/products
	 *
	 * @pre		none
	 * @post	none
	 *
	 * @param	Request	$request
	 * @return	mixed
	 */
	public function ShowProductsPage(Request $request)
	{
		$this->LogFunction("ShowProductsPage()");

		#
		# {FIX_2017-10-26} ShowProductsPage() - Converted to app('store')
		#
		$store = app('store');
		$stores = Store::all();
		$categories = Category::all();
		$store_id = $store->id;
		$category_id = 0;
		$this->LogMsg("Default store ID [".$store->id."]");
		$query = $request->input();
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					$this->LogMsg("Checking query N= $n while V= $v");
					if($n=="s") $store_id = $v;
					if($n=="c") $category_id = $v;
				}
			}
		}
		$this->LogMsg("Required store ID [".$store_id."]");
		$this->LogMsg("Required Category ID [".$category_id."]");

		$products = array();
		#
		# Get all products in the matching category
		#
		if(($store_id > 0)&&($category_id>0))
		{
			$this->LogMsg("Processing for Store/Category [".$store_id."/".$category_id."]");
			$products_in_category = CategoryProduct::where('category_id', $category_id )->get();
			foreach($products_in_category as $pic)
			{
				array_push($products, Product::find($pic->product_id) );
			}
		}
		elseif($store_id > 0)
		{
			$this->LogMsg("Processing for Store Only [".$store_id."/".$category_id."]");
			#
			# {FIX_2018-02-25} Disabled code that gets all products for all categories for store.
			# get all products in all categories for the selected store
			#
#			$store_categories = Category::where('category_store_id',$store_id)->get();
#			$this->LogMsg("   There are [".count($store_categories)."] categories");
#			foreach($store_categories as $sc)
#			{
#				$products_in_category = CategoryProduct::where('category_id', $sc->id )->get();
#				$this->LogMsg("   Category [".$sc->id."]  Number of Products [".count($products_in_category)."]");
#				foreach($products_in_category as $pic)
#				{
#					array_push($products, Product::find($pic->product_id) );
#				}
#			}
			#
			# {FIX_2018-02-25} Converted to first category to reduce number of products show
			#
			$first_category = Category::where('category_store_id',$store_id)
				->where('category_status',"A")
				->first();
			$products_in_category = CategoryProduct::where('category_id', $first_category->id )->get();
			foreach($products_in_category as $pic)
			{
				array_push($products, Product::find($pic->product_id) );
			}
		}
		else
		{
			$this->LogMsg("Processing for All Products [".$store_id."/".$category_id."]");
			#
			# Just get ALL products
			#
			$products = Product::where('prod_status',"A")->get();
		}
		return view('Admin.Products.products',[
			'store_id'=>$store_id,
			'category_id'=>$category_id,
			'store'=>$store,
			'stores'=>$stores,
			'categories'=>$categories,
			'products'=>$products
			]);
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





	/**
	 * Given the ID of a product remove it totally from the server.
	 * use a form because only admin can get the form and the ID
	 * must be encoded in the form and the call must be authenticated as an admin user.
	 *
	 * @param	integer	$id		Product to delete
	 * @return	mixed
	 */
	public function DeleteProduct(Request $request, $id )
	{
		$this->LogFunction("DeleteProduct(".$id.")");

		$form = Input::all();
		if(array_key_exists('id',$form))
		{
			if($id == $form['id'])
			{
				$this->LogMsg("Dispatch Job.");
				$cmd = new DeleteProductJob($id);
				$this->dispatch($cmd);
			}
			else
			{
				$this->LogError("Mismatched product id.");
				\Session::flash('flash_error',"ERROR - Product ID is invalid!");
			}
		}
		else
		{
			$this->LogError("Invalid product id.");
			\Session::flash('flash_error',"ERROR - Product ID is invalid!");
		}
		return $this->ShowProductsPage($request);
	}




	/**
	 * Given the ID of an image remove it totally from the server.
	 * Return back to the product edit page we were on when we pressed delete.
	 *
	 * @param	integer	$id		Image to delete
	 * @param	integer	$pid	Product ID to return to
	 * @return	mixed
	 */
	public function DeleteImage($id,$pid)
	{
		$this->LogFunction("DeleteImage(".$id.",".$pid.")");

		$text = "Image ID [".$id."]";
		$this->LogMsg($text);
		$text = "Product ID [".$pid."]";
		$this->LogMsg($text);

		$cmd = new DeleteImageJob($id);
		$this->dispatch($cmd);
		return $this->ShowEditProductPage($pid);
	}





	/**
	 * Save the image thats been uploaded for this product
	 *
	 * TODO - more work needed on this.
	 *
	 * @return void
	 */
	public function SaveUploadedImage($id)
	{
		$this->LogFunction("SaveUploadedImage(".$id.")");

		$file_data = Input::file('file');
		if($file_data)
		{
			$this->LogMsg("Processing File Data");
			$file_type = explode("/",$file_data->getClientMimeType());
			$text = "File Data ".print_r($file_type,true);
			$this->LogMsg($text);
			if($file_type[0]=="image")
			{
				$extension = $file_type[1];
				$filename = $file_data->getClientOriginalName();
				$subpath = $this->getStorageSubPath($id+0);
				$filepath = $this->getStoragePath($id+0);
				#
				# if no images mapped then parent image is "product_id"-"image_order"."extension"
				#        otherwise, access prod_image_maps and determine the order of images, so increment image order.
				#
				$base_images = ProdImageMap::where('product_id',$id)->get();
				$image_index = 1;
				if(sizeof($base_images)>0)
				{
					#
					# fetch all image db records and parse the names for the indexes already assigned.
					#
					# testing
					$this->LogMsg("Process each image.");
					foreach($base_images as $bi)
					{
						$text = "IDX [".$image_index."]";
						$this->LogMsg($text);
						$text = "DATA: ".print_r($bi,true);
						$this->LogMsg($text);
						$img = Image::where('id',$bi->image_id)->first();
						$file_name = explode(".",$img->image_file_name);
						$f_n_parts = explode("-",$file_name[0]);
						$text = "DATA ".print_r($f_n_parts, true);
						$this->LogMsg( $text );
						if($f_n_parts[1] == $image_index)
						{
							$this->LogMsg("Increment image index!");
							$image_index++;
						}
						if($f_n_parts[1] > $image_index)
						{
							$this->LogMsg("Increment image sequence!");
							$image_index = $f_n_parts[1]+1;
						}
					}
				}

				$id_name = $id."-".$image_index.".".$extension;
				$text = "New ID [".$id_name."]";
				$this->LogMsg($text);

				$file_data->move($filepath,$id_name);
				$newname = $filepath."/".$id_name;
				$text = "New File name [".$newname."]";
				$this->LogMsg($text);

				list($width, $height, $type, $attr) = getimagesize($newname);
				$size = filesize($newname);
				$o = new Image;
				$o->image_file_name = $id_name;
				$o->image_folder_name = $subpath;
				$o->image_size = $size;
				$o->image_height = $height;
				$o->image_width = $width;
				$o->image_parent_id = 0;
				$o->image_order = 0;

				$this->LogMsg("Create Image Entry");
				$o->save();
				$iid = $o->id;
				$text = "New Image ID [".$iid."]";
				$this->LogMsg($text);
				#
				# Use Eloquent to insert into Pivot table
				#
				$image = Image::find($iid);
				$image->products()->attach($id);

				$this->LogMsg("Dispatch resize job");
				dispatch( new ResizeImages($id, $iid) );
				$this->LogMsg("Back in Controller");
			}
			else
			{
				$this->LogError("Invalid file type.");
				\Session::flash('flash_error',"ERROR - Only images are allowed!");
			}
		}
		$this->LogMsg("function Done");
	}




	/**
	 * Present product images and show an upload form
	 * $id is product id to use.
	 *
	 * GET ROUTE: /admin/prodimage/edit/{id}
	 *
	 * {FIX_2017-10-24} Refactored product fetch using eloquent call in ShowImageUploadPage()
	 *
	 * @param $id int - row ID from products table
	 * @return mixed - view object
	 */
	public function ShowImageUploadPage($id)
	{
		$this->LogFunction("ShowImageUploadPage(".$id.")");

		$product = Product::find($id);
		$images = ProdImageMap::where('product_id',$id)->get();
		return view('Admin.Products.editproductimages',[ 'product'=>$product, 'images'=>$images ]);
	}



	/**
	 * POST ROUTE: /admin/prodimage/update/{id}
	 *
	 * ToDo
	 * @pre none
	 * @post none
	 * @param $id int row id of product
	 * @return void
	 */
	public function SaveProdImages($id)
	{
		#
		# @tod Add code to save the image etc
		#
		$this->LogFunction("SaveProdImages(".$id.")");
	}



	/**
	 * Render a view edit page, first collect the existing data and
	 * format it up for the view we are about to call.
	 *
	 * GET ROUTE: /admin/product/edit/{id}
	 *
	 * @param $id int row id to be checked against before insert
	 * @return mixed - view object
	 */
	public function ShowEditProductPage($id)
	{
		$this->LogFunction("ShowEditProductPage(".$id.")");

		$product = Product::find($id);
		$store = app('store');
		$stores = Store::all();
		$categories = Category::all();
		$product_types = ProductType::all();

		$imagemap = ProdImageMap::where('product_id',$id)->get();
		$prod_categories = CategoryProduct::where('product_id',$id)->get();

		$images = array();
		foreach($imagemap as $mapping)
		{
			$this->LogMsg("Found image ID [".$mapping->image_id."]");
			$img = Image::find($mapping->image_id);
			$this->LogMsg("           Name[".$img->image_file_name."]");
			array_push($images, $img);
		}
		$text = "There are ".sizeof($images)." images assembled.";
		$this->LogMsg( $text );

		return view('Admin.Products.editproduct',[
			'product'=>$product,
			'product_types'=>$product_types,
			'images'=>$images,
			'categories'=>$categories,
			'store'=>$store,
			'stores'=>$stores,
			'catmappings'=>$prod_categories]);
	}




	/**
	 * Call the view to present the "Add New" Product page
	 *
	 * GET ROUTE: /admin/product/addnew
	 *
	 * @return mixed - view object
	 */
	public function ShowAddProductPage()
	{
		$this->LogFunction("ShowEditProductPage()");

		$categories = Category::where('category_status','A')->orderBy('category_title')->get();
		$stores = Store::all();
		$product_types = ProductType::all();

		return view('Admin.Products.addproduct',[
			'categories'=>$categories,
			'product_types'=>$product_types,
			'stores'=>$stores
			]);
	}



	/**
	 * Update the products table with our changes (if any).
	 *
	 * POST ROUTE: /admin/product/update/{id}
	 *
	 * @pre form must present all valid columns
	 * @post new row inserted into database table "products" 
	 * @param $request mixed Validation request object
	 * @param $id int row id to be checked against before insert
	 * @return mixed - view object
	 */
	public function UpdateProduct(ProductRequest $request, $id)
	{
		$this->LogFunction("UpdateProduct(".$id.")");

		CategoryProduct::where('product_id',$id)->delete();
		$categories = $request->category;	/* array of category id's */
		if(sizeof($categories)>0)
		{
			$this->LogMsg("Assign categories");
			foreach($categories as $c)
			{
				$text = "Assign category ID [".$c."] with product ID [".$id."]";
				$this->LogMsg( $text );
				$o = new CategoryProduct;
				$o->category_id = $c;
				$o->product_id = $id;
				$o->save();
				$this->LogMsg("Insert ID [".$o->id."]");
			}
		}
		else
		{
			$this->LogMsg("No categories to process!");
		}
		#
		# get the product and if the qty was 0 and is now >0 then call Back In Stock
		#
		$product = Product::find($id);
		$this->LogMsg("Check stock levels for Product [".$id."] - [".$product->prod_sku."]");
		$store = app('store');
		if($product->prod_qty == 0)
		{
			$this->LogMsg("Existing stock level is 0");
			if($request['prod_qty'] > 0)
			{
				$this->LogMsg("Stock Level increased to [".$request['prod_qty']."]");
				$notify_list = Notification::where('product_code',$product->prod_sku)->get();
				$this->LogMsg("Count of notifications to send is [".sizeof($notify_list)."]");
				foreach($notify_list as $n)
				{
					if(strlen($n->email_address)>3)
					{
						$this->LogMsg("Send notify to [".$n->email_address."]");
						$cmd = new BackInStock($store, $n->email_address, $product);
						dispatch($cmd);
					}
					$n->delete();
				}
			}
		}
		$request['id'] = $id;
		ProductService::update($request);
		$this->SaveUploadedImage($id);
		return $this->ShowProductsPage($request);
	}




	/**
	 * Save the form just posted back to the server
	 * Handle the images (if any) separately.
	 *
	 * POST ROUTE: /admin/product/save
	 *
	 * @pre form must present all valid columns
	 * @post new row inserted into database table "products" 
	 * @param $request mixed Validation request object
	 * @return mixed - view object
	 */
	public function SaveNewProduct(ProductRequest $request)
	{
		$this->LogFunction("SaveNewProduct()");

		$pid=0;
		$categories = $request->categories;	/* array of category id's */
		$pid = ProductService::insert($request);
		$this->LogMsg("Insert New Product new ID [".$pid."]");
		$this->LogMsg("Process product assigned categories");
		if(isset($categories))
		{
			foreach($categories as $c)
			{
				$o = new CategoryProduct;
				$o->product_id = $pid;
				$o->category_id = $c;
				$o->save();
				$this->LogMsg("Insert product ID [".$pid."] with Category ID [".$c."]");
			}
		}
		else
		{
			#
			# @todo Need to assign to somethign or else product will not show up in list!
			#
			$this->LogMsg("No categories to assign (yet)");
		}
		$this->SaveUploadedImage($pid);
		return $this->ShowProductsPage($request);
	}



	/**
	 * Create the path needed to store product images and return the full filesystem path to place file.
	 *
	 * @pre		none
	 * @post	creates directory structure as needed
	 *
	 * @param	integer	$id - the product ID
	 * @return	string 
	 */
	protected function getStoragePath( $id )
	{
		$this->LogFunction("getStoragePath(".$id.")");
		$path="";
		$length = strlen($id);
		$id = "".$id."";
		for($i=0 ; $i < $length ; $i++)
		{
			$path.="/".$id[$i];
		}
		$finalpath = public_path()."/media".$path;
		if(is_dir($finalpath))
		{
			$this->LogMsg("PATH [".$finalpath."]");
			return $finalpath;
		}
		else
		{	
			$this->LogMsg("Create Path [".$finalpath."]");
			try { mkdir($finalpath,0775,true); }
			catch(Exception $e)
			{
				$this->LogError("Failed to create Path [".$finalpath."]");
			}
		}
		$this->LogMsg("PATH [".$finalpath."]");
		return $finalpath;
	}



	/**
	 * Create the subpath needed to store product images and return a partial filesystem path.
	 *
	 * @pre none
	 * @post none
	 *
	 * @param	integer	$id - the product ID
	 * @return	string
	 */
	protected function getStorageSubPath($id)
	{
		$this->LogFunction("getStorageSubPath(".$id.")");

		$path="media";
		$length = strlen($id);
		$id = "".$id."";
		for($i=0 ; $i < $length ; $i++)
		{
			$path.="/".$id[$i];
		}
		$this->LogMsg("PATH [".$path."]");
		return $path;
	}






	/**
	 * Display the product attributes
	 *
	 *
	 * @return	mixed
	 */
	public function ShowAttributesPage()
	{
		$Store = new Store;
		$stores = Store::all():
		$store_names = array();
		$store_names[0]='All Stores';
		$html = "<select class='form-control' id='store_id' name='store_id'>";
		$html .= "<option value='0'>Global - All Stores</option>";
		foreach($stores as $store)
		{
			$store_names[$r->id] = $r->store_name;
			$html .= "<option value='".$store->id."'>".$store->store_name."</option>";
		}
		$html .="</select>";
		$attributes = Attribute::all();

		return view('Admin.Attributes.showattributes',[
			'attributes'=>$attributes,
			'stores'=>$store_names,
			'store_select_list'=>$html
			]);
	}


	/**
	 *------------------------------------------------------------
	 *
	 *                        DEVELOPMENT
	 *
	 *------------------------------------------------------------
	 *
	 * Return parent product details
	 *
	 * GET ROUTE: /pp?PID=nnnnn
	 *
	 * @return	void
	 */
	public function BulkUpdate()
	{
		$this->LogFunction("BulkUpdate()");

		$Product = new Product;
		$products = Product::where('prod_sku','like',"PP-")->get();
		return view("Admin.Products.bulkupdate",['products'=>$products]);
	}




	/**
	 *------------------------------------------------------------
	 *
	 *                        DEVELOPMENT
	 *
	 *------------------------------------------------------------
	 *
	 * Return parent product details
	 *
	 * GET ROUTE: /pp?PID=nnnnn
	 *
	 * @return	void
	 */
	public function DebugParentProducts(Request $request)
	{
		$this->LogFunction("DebugParentProducts()");

		$Product = new Product;
		$query = $request->input();
		$pid = 0;
		foreach($query as $n=>$v)
		{
			if(is_string($n)== true)
			{
				if(is_string($v)== true)
				{
					if($n=="PID") $pid = $v;
				}
			}
		}
		if($pid>0)
		{
			$Product->getChildProducts($pid);
		}
		else
			echo "ERROR - no product set";
	}







}
