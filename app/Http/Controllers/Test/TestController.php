<?php
/**
 * \class	TestController
 *
 * \addtogroup Testing
 * TestController - Provides various manual test routines sending to system_user
 * - 2018-07-17 Decided to standardise the method name and route to test_x_y()
 * - Need to make constructor calls uniform (store, email, object)
 * - Need to convert to Mailable interfaces when sending emails.
 */
namespace App\Http\Controllers\Test;

use Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Jobs\AbandonedCart;
use App\Jobs\AbandonedWeekOldCart;
use App\Jobs\ProcessAbandonedCart;

use App\Jobs\ResizeImages;
use App\Jobs\EmailUserJob;
use App\Jobs\DeleteImageJob;
use App\Jobs\DeleteProductJob;
use App\Jobs\CleanupOrphanImages;

use App\Jobs\EmptyCartJob;

use App\Jobs\LoginFailed;
use App\Mail\LoginFailedEmail;

use App\Jobs\OrderPaid;
use App\Jobs\OrderPlaced;
use App\Jobs\OrderCancelled;
use App\Jobs\OrderCompleted;
use App\Jobs\OrderDispatched;
use App\Jobs\CheckPendingOrders;
use App\Jobs\OrderDispatchPending;

use App\Jobs\BackInStock;
use App\Jobs\OutOfStockJob;
use App\Mail\BackInStockEmail;
use App\Jobs\PostPurchaseEmail;

use App\Jobs\SendWelcome;
use App\Jobs\FinalSubRequest;
use App\Jobs\ReSendSubRequest;
use App\Jobs\ConfirmSubscription;
use App\Jobs\ProcessSubscriptions;
use App\Jobs\SubscriptionConfirmed;
use App\Mail\ConfirmSubscriptionEmail;
use App\Jobs\AutoSubscribeJob;


use App\Jobs\UpdateCartLocks;

use Illuminate\Support\Facades\Mail;
use App\Mail\MailOut;

use App\Mail\AbandonedCartEmail;
use App\Mail\AbandonedWeekOldCartEmail;
use App\Mail\SendWelcomeEmail;

use App\User;
use App\Models\Cart;
use App\Models\Image;
use App\Models\Store;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\CartItem;
use App\Models\Attribute;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use App\Models\Notification;
use App\Models\CategoryImage;
use App\Models\AttributeValue;
use App\Models\AttributeProduct;


use App\Events\Larvela\AddToCartMessage;
use App\Events\Larvela\DispatcherFactory;


use App\Services\ProductFilters\FilterProducts;

/**
 * \brief Manually initiated test framework. Crude but effective.
 */
class TestController extends Controller
{

	public function test_filter_get()
	{
		$o = new FilterProducts;
		echo json_encode($o->getFilters());
	}


	public function test_filter_products()
	{
		$o = new FilterProducts;
		echo json_encode($o->ReturnProducts());
	}


	public function test_pagination_options()
	{
		$store = app("store");
		$settings = StoreSetting::where('setting_store_id',$store->id)->get();
		$pagination_options = array_filter($settings->toArray(),function($setting)
		{
			if($setting['setting_name'] == "PAGINATION_OPTIONS") return true;
		});
#		dd(array_pop($pagination_options));

		$data = json_encode(array("S"=>"OK","O"=>array(array_pop($pagination_options)['setting_value'])));
		dd($data);
	}





	public function test_product_packs()
	{
		$store=app('store');
		$categories = Category::where('category_store_id',0)->get();
		$images = array();
		$parent_product = Product::where('prod_type',5)->first();
		$mapping = $parent_product->images;
		if(sizeof($mapping)>0)
		{
			foreach($mapping as $m)
			{
				array_push($images,Image::find($m->image_id));
				$pp_images = Image::where('image_parent_id',$m->image_id)->get();
				foreach($pp_images as $pi)
				{
					array_push($images,$pi);
				}
			}
		}
#
# @todo build thumb nails
#
		$thumbnails = array();
		$related = array();


		$child_products = $this->getChildProducts($parent_product);
		$attributes = Attribute::where('store_id',$store->id)->get()->toArray();
		#
		# Build list of ID's
		#
		$attr_list = array_map(function($row) { return $row['id']; }, $attributes);
		#
		# Sort and order them
		#
		$attribute_values = AttributeValue::whereIn('attr_id',$attr_list)->orderBy('attr_id')->orderBy('attr_sort_index')->get()->toArray();

		$theme_path = \Config::get('THEME_PRODUCT')."packproduct";
		return view($theme_path,[
			'store'=>$store,
			'categories'=>$categories,
			'attributes'=>$attributes,
			'attribute_values'=>$attribute_values,
			'product'=>$parent_product,
			'child_products'=>$child_products,
			'parent_images'=>$images,
			'thumbnails'=>$thumbnails,
			'related'=>$related
			]);
	}



	/**
	 * Experimental code to compile an SKU using the following combinations:
	 *	Product-Colour
	 *	Product-Size
	 *	Product-Colour-Size
	 *
	 * Need to compile the SKU using the combine_order
	 *
	 *
	 * GET ROUTE: /test/product/show/{id}
	 *
	 * @return	mixed
	 */
	public function test_product_show($id)
	{
		$attributes = Attribute::get();
		echo "<h2>Attributes</h2>";
		foreach($attributes as $a)
		{
			echo "Attribute  ".$a->id." [".$a->attribute_name."]<br>";
		}
		$products = AttributeProduct::get();
		echo "<h2>Products with Attributes</h2>";
		foreach($products as $p)
		{
			echo "Product ID [".$p->product_id."] Attribute ID [".$p->attribute_id."]<br>";
		}
		echo "<h2>Applicable SKU's</h2>";
		$product = Product::find($id);
		if(!is_null($product))
		{
			$attribute_values = AttributeValue::get();
			$product_attributes = AttributeProduct::where('product_id',$id)->orderby('combine_order')->get();
			
			if(sizeof($product_attributes)==1)
			{
				foreach($product_attributes as $pa)
				{
					echo "PID [".$pa->product_id."]  Attr [".$pa->attribute_id."]<br>";
					foreach($attribute_values as $at)
					{
						if($at->attr_id == $pa->attribute_id)
						{
							$child_product = Product::where('prod_sku',$at->attr_value)->first();
							echo "SKU ". $product->prod_sku.'-'.$at->attr_value." - QTY [".$child_product->prod_qty."]<br>";
						}
					}
				}
			}
			elseif(sizeof($product_attributes)==2)
			{
				$first_attributes = AttributeValue::where('attr_id',1)->orderby('attr_sort_index')->get();
				$second_attributes = AttributeValue::where('attr_id',2)->orderby('attr_sort_index')->get();
				foreach($first_attributes as $a1)
				{
					foreach($second_attributes as $a2)
					{
						$qty = 0;
						$sku = $product->prod_sku.'-'.$a1->attr_value."-".$a2->attr_value;
						$child_product = Product::where('prod_sku',$sku)->first();
						if(!is_null($child_product))
						{
							$qty = $child_product->prod_qty;
						}
						echo "SKU ". $sku." - QTY [".$qty."]<br>";
					}
				}
			}
		}
		else
		{
			echo "Invalid product ID!";
		}
	}


	protected function getChildProducts($product)
	{
		$products = array();
		if(!is_null($product))
		{
			$attributes = Attribute::get();
			$attribute_values = AttributeValue::get();
			$product_attributes = AttributeProduct::where('product_id',$product->id)->orderby('combine_order')->get();
			
			if(sizeof($product_attributes)==1)
			{
				foreach($product_attributes as $pa)
				{
					foreach($attribute_values as $at)
					{
						if($at->attr_id == $pa->attribute_id)
						{
							$child_product = Product::where('prod_sku',$at->attr_value)->first();
							array_push($products,$child_product);
						}
					}
				}
			}
			elseif(sizeof($product_attributes)==2)
			{
				$first_attributes = AttributeValue::where('attr_id',1)->orderby('attr_sort_index')->get();
				$second_attributes = AttributeValue::where('attr_id',2)->orderby('attr_sort_index')->get();
				foreach($first_attributes as $a1)
				{
					foreach($second_attributes as $a2)
					{
						$qty = 0;
						$sku = $product->prod_sku.'-'.$a1->attr_value."-".$a2->attr_value;
						$child_product = Product::where('prod_sku',$sku)->first();
						if(!is_null($child_product))
						{
							array_push($products,$child_product);
						}
					}
				}
			}
		}
		return $products;
	}



	// test/fs
	public function fs()
	{
		$store=app('store');
		dispatch(new FinalSubRequest($store, $email));
	}



	/**
	 * GET ROUTE: /test/url
	 *
	 * @return	mixed
	 */
	public function test_url()
	{
		Config::set("app.url", "https://buggsbunny.com");
		$url = Config::get("app.url");
		echo "<h1>URL [".$url."] <h1>";
		$store=app('store');
		dd($store);
	}



	public function test_footer()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		$customer = Customer::where('customer_email',$email)->first();
		$order = Order::find(28);
		$order_items = OrderItem::where('order_item_oid',$order->id)->get();
		$hash="1234";
		return view('Mail.RD.order_paid',[
			'store'=>$store,
			'email'=>$email,
			'customer'=>$customer,
			'order'=>$order,
			'order_items'=>$order_items,
			'hash'=>$hash
			]);
	}



/*============================================================
 *
 *
 *                    STOCK NOTIFICATIONS
 *
 *
 *============================================================
 */

	public function test_backinstock()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		$product = Product::find(1);
		$hash="1234";
		return view('Mail.RD.back_in_stock',[
			'store'=>$store,
			'email'=>$email,
			'product'=>$product,
			'hash'=>$hash
			]);
	}





/*============================================================
 *
 *
 *                      SUBSCRIPTIONS
 *
 *
 *============================================================

	/**
	 * GET ROUTE: /test/subscription/confirm
	 *
	 * @return	mixed
	 */
	public function test_subscription_confirm()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new ConfirmSubscription($store, $email));
		Mail::to($email)->send(new ConfirmSubscriptionEmail($store, $email));
	}



	/**
	 * GET ROUTE: /test/subscription/confirmed
	 *
	 * @return	mixed
	 */
	public function test_subscription_confirmed()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new SubscriptionConfirmed($store, $email));
	}



	/**
	 * GET ROUTE: /test/subscription/sendwelcome
	 *
	 * @return	mixed
	 */
	public function test_subscription_sendwelcome()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new SendWelcome($store,$email));	
		Mail::to($email)->send(new SendWelcomeEmail($store, $email));
		dd($this);
	}



	/**
	 * GET ROUTE: /test/subscription/resend
	 *
	 * @return	mixed
	 */
	public function test_subscription_resend()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new ReSendSubRequest($store,$email));	
	}



	/**
	 * GET ROUTE: /test/subscription/finalrequest
	 *
	 * @return	mixed
	 */
	public function test_subscription_finalrequest()
	{
		$store=app('store');
		$email = Config::get("app.test_email");
		dispatch(new FinalRequest($store,$email));	
	}



/*============================================================
 *
 *
 *                         Category & Images
 *
 *
 *============================================================
 */

	public function test_category_image()
	{
		$store = app('store');
		$email = Config::get("app.test_email");
		$category = Category::find(13);
		$pivot = $category->images;
		$count=0;
		foreach($pivot as $p)
		{
			echo "<pre>";
			print_r($p->id);
			echo "</pre>";
			$count++;
		}
		echo "<p>".$count." Records found</p>";
		$image = Image::find(91);

# attach the image to the pivot table
#
#		$category->images()->attach($image);
#
# can insert the ID using sync(array[])
#
#		$category->images()->sync( array(91,107) );
#
#		$category->images()->detach( array(91,107) );
#
	}
}
