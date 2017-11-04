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
use	App\Models\ProdImageMaps;
use App\Models\ImageProduct;
use App\Models\CategoryProduct;
use App\Models\Notifications;


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
     * Execute the job, remove all refereneces to it and then remove the product itself.
     *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("-- DeleteProductJob -- handle()");
		$Image = new Image;
		$ProdImageMaps = new ProdImageMaps;
		$ImageProduct = new ImageProduct;
		$CategoryProduct = new CategoryProduct;

		$product = Product::where('id', $this->product_id )->first();
		Notifications::where('product_code',$product->prod_sku)->delete();

		#
		# get each image associated with the row, and remove the image.
		#
		$rows = $ImageProduct->getByProductID( $this->product_id );
		foreach($rows as $r)
		{
			$img = Image::where('id',$r->id)->first();
			if(sizeof($img)>0)
			{
				$this->LogMsg( "Remove image ".$img->image_file_name );
				$path = $r->image_folder_name."/".$img->image_file_name;
				$finalpath = public_path()."/media/".$path;
				unlink($path);
				$rv = $Image->DeleteByID($r->id);
				$this->LogMsg("Image [".$r->id."] - removed [".$rv."]");
			}
		}
		$rv = $ImageProduct->DeleteByProductID( $this->product_id );
		$this->LogMsg("Pivot Table - image_product rows removed [".$rv."]");

		$rv = $CategoryProduct->DeleteByProductID( $this->product_id );
		$this->LogMsg("Pivot Table - category_product rows removed [".$rv."]");

		$rv = Product::where('id', $this->product_id )->delete();
		$this->LogMsg("Product Removed [".$rv."]");
    }
}
