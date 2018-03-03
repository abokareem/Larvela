<?php
/**
 * \class	CleanupOrphanImages
 * \date	2018-02-25
 *
 * \addtogroup CRON
 * Get all parent images and then see if they are attached to a parent.
 * - If there is no matching product withthe same ID then
 *   - remove thumb nails
 *   - remove image
 * If the parent exists
 * - check that they have a mapping in image_product pivot table
 *  - if not - add it.
 *  - if yes - skip it.
 */
namespace App\Jobs;

use App\Models\Product;
use App\Models\Image;
use App\Models\ProdImageMaps;

use App\Traits\Logger;


/**
 * CRON task to cleanup orphan images that remain after a product was deleted but something went wrong.
 */
class CleanupOrphanImages extends Job
{
use Logger;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job by calling the Run() method.
     *
     * @return void
     */
    public function handle()
    {
		$this->Run();
    }



    /**
     * Locate orders that are on waiting status and more than 7 days old.
     * Reverse the order and inform user.
	 *
     * @return void
     */
	public function Run()
	{
		$this->setFileName("store-cron");
		$this->LogStart();

		$this->LogMsg("Fetch all parent images");
		$images = Image::where('image_parent_id',"0")->get();
		$this->LogMsg("There are [".sizeof($images)."] images");

		foreach($images as $image)
		{
			$parts = explode("-",$image->image_file_name);
			$count = Product::where('id',$parts[0])->count();
			if($count == 0)
			{
				$this->LogMsg("There is no Product with an ID of [".$parts[0]."].");
				$thumbnails = Image::where('image_parent_id',$image->id)->get();
				$this->LogMsg("Found [".sizeof($thumbnails)."] thumbnails for image [".$image->id."]");
				foreach($thumbnails as $thumbnail)
				{
					$this->LogMsg("Found thumbnail [".$thumbnail->id."]");
				}
				#
				# remove all in one hit
				#


				#
				# remove image
				#
			}
			else
			{
				$this->LogMsg("Product ID [".$parts[0]."] present.");
			}
		}

		$this->LogEnd();
	}
}
