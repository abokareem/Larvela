<?php
/**
 * \class	DeleteProductJob
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-01
 * \version	1.0.2
 * 
 * 
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * \addtogroup Internal
 * DeleteProductJob - Delete a product and all references to it.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use App\Jobs\EmailUserJob;

use App\Traits\Logger;

use	App\Models\Image;
use App\Models\Product;
use	App\Models\ProdImageMap;
use App\Models\ImageProduct;
use App\Models\Notification;
use App\Models\CategoryProduct;
use App\Models\AttributeProduct;


/**
 * \brief Delete a product after removing all references to it.
 * -  Email the store owner that its been removed
 */
class DeleteProductJob extends Job 
{
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
		$this->setFileName("larvela-admin");
		$this->setClassName("DeleteProductJob");
		$this->LogStart();
		$this->product_id = $product_id;
    }



    /**
     * Log the job has cleaned up
     *
     * @return	void
     */
	public function __destruct()
	{
		$this->LogEnd();
	}




    /**
     * Remove all references to product and then remove the product itself.
     *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("handle()");
		$Image = new Image;

		$this->LogMsg("Fetch product ID [".$this->product_id."]");
		$product = Product::find($this->product_id);

		$this->LogMsg("Cleanup any customer notification requests");
		Notification::where('product_code',$product->prod_sku)->delete();

		AttributeProduct::where('product_id',$product->id)->delete();
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
