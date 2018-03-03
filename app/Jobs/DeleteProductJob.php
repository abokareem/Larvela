<?php
/**
 * \class	DeleteProductJob
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-01
 *
 * [CC]
 *
 * \addtogroup Internal
 * DeleteProductJob - Delete a product and all references to it.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;

use App\Traits\TemplateTrait;
use App\Traits\Logger;

use	App\Models\Image;
use App\Models\Product;
use	App\Models\ProdImageMap;
use App\Models\ImageProduct;
use App\Models\CategoryProduct;
use App\Models\Notification;


/**
 * \brief Delete a product from the store (this job is TODO).
 *
 * Given the product ID, remove all references to it and then remove the product itself.
 * Email the store owner that its been removed
 */
class DeleteProductJob extends Job 
{
use TemplateTrait;
use Logger;


/**
 * The row ID of the product in the "products" table.
 * @var int $product_id
 */
protected $product_id;



    /**
     * Create a new job instance.
     *
     * @param	integer	$product_id	The row ID
     * @return	void
     */
    public function __construct($product_id)
    {
		$this->setFileName("store-admin");
		$this->LogFunction("-- DeleteProductJob -- Constructor");
		$this->product_id = $product_id;
    }



    /**
     * Log the job has cleaned up
     *
     * @return	void
     */
	public function __destruct()
	{
		$this->LogFunction("-- DeleteProductJob -- Destructor");
	}




    /**
     * Remove all references to product and then remove the product itself.
     *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("-- DeleteProductJob -- handle()");
		$Image = new Image;

		$this->LogMsg("Fetch product ID [".$this->product_id."]");
		$product = Product::find($this->product_id);

		$this->LogMsg("Cleanup any customer notification requests");
		Notification::where('product_code',$product->prod_sku)->delete();

		$this->LogMsg("Fetch images id from pivot table");
		$images = $product->images()->get();
		$this->LogMsg("There are [".sizeof($images)."] images associated with this product");
		
		$pivot_rows = ProdImageMap::where('product_id',$product->id)->get();
		foreach($pivot_rows as $row)
		{
			$this->LogMsg("Removing Pivot Table entry [".$row->id."]");
			$row->delete();
		}
		$this->LogMsg("Pivot Table - image_product rows removed");

		#
		# Remove any thumbnails associated with each base image.
		#
		foreach($images as $img)
		{
			$rv = Image::where('image_parent_id', $img->id)->delete();
			$this->LogMsg("Removed [".$rv."] thumbnails");

			$this->LogMsg( "Remove image ".$img->image_file_name );
			$path = $img->image_folder_name."/".$img->image_file_name;
			$finalpath = public_path()."/media/".$path;
			unlink($path);

			$rv = $img->delete();
			$this->LogMsg("Base (parent) Image removed  RC=[".$rv."]");
		}

		$rv = CategoryProduct::where('product_id', $this->product_id )->delete();
		$this->LogMsg("Pivot Table - category_product rows removed [".$rv."]");

		$rv = $product->delete();
		$this->LogMsg("Product Removed [".$rv."]");
    }
}
