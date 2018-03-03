<?php
/**
 * \class	ResizeImage
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-04-20
 *
 * [CC]
 *
 * \addtogroup Internal
 * ResizeImage - Given a base image, resize into a series of thumbnails.
 */
namespace App\Jobs;


use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;

use App\Models\Image;
use App\Models\ProdImageMap;

use App\Traits\Logger;

/**
 * \brief Given a base image, resize into a series of thumbnails.
 *
 * Given the Product ID and the Image ID, retrieve image and build
 * a series of smaller images that are then mapped to the parent Image.
 * These are used on main page and product page.
 */
class ResizeImages extends Job
{
use Logger;


/**
 * The product row id from the "products" table.
 * @var	int $product_id
 */
protected $product_id;


/**
 * The image row id from the "images" table.
 * @var	int $image_id
 */
protected $image_id;


/**
 * location of conversion program (image majik).
 * @var string $CONVERT
 */
protected $CONVERT = "/usr/bin/convert";


    /**
     * Create a new job instance and save the product and image ID's.
     *
     * @param	int	$product_id	row id from products table.
     * @param	int	$image_id	row id from images table.
     * @return void
     */
    public function __construct($pid, $iid)
    {
		$this->setFileName("store");
		$this->LogStart();
		$this->LogFunction("-- ResizeImageJob -- Constructor");
		$this->product_id = $pid;
		$this->image_id = $iid;
    }


	public function __destruct()
    {
		$this->LogFunction("-- ResizeImageJob -- Destructor");
		$this->LogEnd();
	}




    /**
     * Create a series of thumb nails.
	 * 68x68 thumbnail for product page
     * 310x310 and 510x510 for category and front page
	 *
     * @return void
     */
    public function handle()
    {
		$this->LogFunction("-- ResizeImageJob -- Handler");
		$Image = new Image;
		$PIM = new ProdImageMap;

		$img = Image::find($this->image_id);

		$image_source_path = public_path()."/".$img->image_folder_name;
		$this->LogMsg("Image Source Path: ".$image_source_path );

		$this->LogMsg( "Product ID [".$this->product_id."]" );
		$this->LogMsg( "Image ID [".$this->image_id."]" );
		$this->LogMsg( "Image SRC Path [".$image_source_path."]" );
		$text = print_r($img, true);
		$this->LogMsg( "IMG Row Data: ".$text ); 

		chdir($image_source_path);
		list($width, $height, $type, $attr) = getimagesize($img->image_file_name);
		list($name, $extension) = explode(".", $img->image_file_name);
		$i = 1;
		$image_sizes = array("68x68", "310x310", "510x510");
		foreach($image_sizes as $size)
		{
			$this->LogMsg( "Create thumbnail: ".$size."]" );
			$new_name = $this->image_id."-".$size.".".$extension;
			$this->ConvertImage($image_source_path, $img->image_file_name, $new_name, $size);
			$data = array();
			#
			# insert into images with parent id set to this->image_id
			# insert into product image map with the ID and this->product_id
			#
			list($width, $height, $type, $attr) = getimagesize( $image_source_path."/".$new_name);
			$this->LogMsg("Save new Image [".$new_name."]");
			$o = new Image;
			$o->image_file_name = $new_name;
			$o->image_folder_name = $img->image_folder_name;
			$o->image_size   = filesize( $image_source_path."/".$new_name );
			$o->image_height = $height;
			$o->image_width  = $width;
			$o->image_order  = $i++;
			$o->image_parent_id = $this->image_id;
			$o->save();
			#
			# insert this image into the DB as a thumbnail	
			# only parent images are in "prod_image_maps table
			#
			foreach($data as $n=>$v)
			{
				$this->LogMsg("DATA [".$n."] = [".$v."]" );
			}
			$this->LogMsg( "Done! --- New Name: [".$new_name."]" );
		}
	}



	/**
	 * Convert the image into the new size.
	 *
	 * @param $dir_name Directory to change into where src file is.
	 * @param $image_name the source image file name
	 * @param $new_name the resultant file after conversion.
	 * @param $size String of HxW size
	 * @return void
	 */
	public function  ConvertImage($dir_name, $image_name, $new_name, $size)
	{
		$this->LogFunction("Convert()");
		$this->LogMsg( "DIR [".$dir_name."]" );
		$this->LogMsg( "IMG [".$image_name."]" );
		$this->LogMsg( "NEW [".$new_name."]" );
		$this->LogMsg( "SIZE[".$size."]" );
		chdir($dir_name);
		$command = $this->CONVERT." ".$image_name." -resize ".$size."! ".$new_name;
		$rv = exec($command);
		$this->LogMsg( "CMD[".$command."]" );
		$this->LogMsg( "RV [".$rv."]" );
    }
}
