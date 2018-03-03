<?php
/**
 * \class	ProductTypeController
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-01-13
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


use App\Traits\Logger;


/**
 * \brief MVC Controller to Handle the Product Type Administration functions.
 */
class ProductTypeController extends Controller
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
		$this->LogMsg("CLASS:ProductTypeController");
	}
	
	/**
	 * Close log file
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogMsg("CLASS:ProductTypeController");
		$this->LogEnd();
	}




	/**
	 * GET ROUTE: /admin/producttypes
	 *
	 *
	 * {FIX_2017-10-24} Changed get all product types to Eloquent call
	 *
	 * @return	void
	 */
	public function Show()
	{
		$pt = ProductType::all();
		return view('Admin.ProductTypes.showtypes',['product_types'=>$pt]);
	}



	/**
	 * GET ROUTE: /admin/producttype/addnew
	 *
	 * @return	mixed
	 */
	public function Add()
	{
		return view('Admin.ProductTypes.addtype');
	}



	/**
	 * POST ROUTE: /admin/producttype/save
	 *
	 * @return	mixed
	 */
	public function Save(Request $request)
	{
		$o = new ProductType;
		$o->product_type = $request['product_type'];
		if(($rv=$o->save()) > 0)
		{
			$this->LogMsg("Product Type [".$o->product_type."] saved!");
			\Session::flash('flash_message',"Product Type saved!");
		}
		else
		{
			$this->LogError("Save filed!");
			\Session::flash('flash_error',"Product Type save failed!");
		}
		return $this->Show();
	}


	/**
	 * GET ROUTE: /admin/producttype/edit/{id}
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Edit($id)
	{
		$pt = ProductType::find($id);
		return view('Admin.ProductTypes.edittype',['product_type'=>$pt]);
	}
	


	
	/**
	 * POST ROUTE: /admin/product/update/{id}
	 *
	 * @param	integer	$id
	 * @return	mixed
	 */
	public function Update(Request $request, $id)
	{
		$this->LogFunction("UpdateProductType()");
		$ProductType = new ProductType;
		if(is_numeric($id))
		{
			if($id == $request['id'])
			{
				$this->LogMsg("Update Product Type [".$request['product_type']."]");
				$o = ProductType::find($id);
				$o->product_type = $request['product_type'];
				if(($rv=$o->save()) > 0)
				{
					$this->LogMsg("Updated!");
					\Session::flash('flash_message',"Product Type updated!");
				}
				else
				{
					\Session::flash('flash_message',"No update performed!");
					$this->LogMsg("No Update performed!");
				}
			}
		}
		return $this->Show();
	}




	/**
	 * POST ROUTE: / put route here
	 *
	 * {FIX_2017-10-26} Added method DeleteProductType()
	 *
	 * @param	integer	$id
	 * @return	void
	 */
	public function Delete($id)
	{
		ProductType::find($id)->delete();
		return $this->Show();
	}
}
