<?php
/**
 * \class	CleanupOrphanImages
 * \date	2018-02-25
 * \author	Sid Young <suid@off-grid-engineering.com>
 * \version	1.0.0
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
 * \addtogroup CRON
 * Get all parent images and then see if they are attached to a parent.
 * - If there is no matching product withthe same ID then
 * - remove thumb nails
 * - remove image
 * If the parent exists
 * - check that they have a mapping in image_product pivot table
 * - if not - add it.
 * - if yes - skip it.
 */
namespace App\Jobs;


use App\Models\Product;
use App\Models\Image;
use App\Models\ProdImageMaps;


use App\Traits\Logger;


/**
 * \brief CRON task to cleanup orphan images that remain after a product was deleted but something went wrong.
 */
class CleanupOrphanImages extends Job
{
use Logger;

    /**
     * Create a new job instance.
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
		$this->setFileName("larvela-cron");
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
