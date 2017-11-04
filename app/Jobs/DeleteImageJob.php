<?php
/**
 * \class	DeleteImageJob
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date 	016-08-30
 * @category	Job
 *
 * [CC]
 *
 * \addtogroup Internal
 * DeleteImageJob - Delete an image from the file system and database.
 */
namespace App\Jobs;



use App\Jobs\Job;

use App\Models\Image;
use App\Models\ProdImageMaps;


use App\Traits\Logger;

/**
 * \brief Delete an image from the store.
 *
 * Given the image ID, locate the image, its thumbnails and remove any mapping to the image. Then remove the image itself.
 */
class DeleteImageJob extends Job
{
use Logger;


/**
 * The row id from the "images" table.
 * @var int $image_id
 */
protected $image_id;

    /**
     * Create a new job instance and save the image ID.
     *
     * @return void
     */
    public function __construct($image_id)
    {
		$this->setFileName("store-admin");
		$this->LogFunction("-- DeleteImageJob -- Constuctor");
		$this->image_id = $image_id;
    }


	/**
	 * @return	void
	 */
	public function __destruct()
	{
		$this->LogFunction("-- DeleteImageJob -- Destuctor");
	}




    /**
     * Remove the image and all mappings to it.
     *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("-- DeleteImageJob -- handle()");
   		$Image = new Image;
		$ProdImageMaps = new ProdImageMaps;

		$img = $Image->getByID($this->image_id);
		$file_name = $img->image_file_name;
		$folder = $img->image_folder_name;
		$path = public_path()."/".$folder."/".$file_name;
		if(file_exists($path))
		{
			$this->LogMsg("Unlinking file [".$path."]" );
			unlink($path);
		}
		$mapping = $ProdImageMaps->getByImageID($this->image_id);
		foreach($mapping as $map)
		{
			$this->LogMsg( "Removing Mapping ID=".$map->id."  P=".$map->product_id." I=".$map->image_id ); 
			$ProdImageMaps->DeleteByID($map->id);
			$Image->DeleteByID($img->id);
		}
		$this->LogMsg( "Get all thumb nails." );
		$thumbnails = $Image->getByParentID($this->image_id);
		foreach($thumbnails as $tb)
		{
			$this->LogMsg( "Thumbnail ID [".$tb->id."] - File [".$tb->image_file_name."]" );
			$text = print_r($tb, true);
			$this->LogMsg( "DATA [".$text."]" );
			$file_name = $tb->image_file_name;
			$folder = $tb->image_folder_name;

			$rv = $Image->DeleteByID($tb->id);

			$this->LogMsg( "Delete call returned [".$rv."]" );	
			$path = public_path()."/".$folder."/".$file_name;
			if(file_exists($path))
			{
				$this->LogMsg( "Unlink thumbnail - File [".$path."]" );
				unlink($path);
			}
		}
    }
}
