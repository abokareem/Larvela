<?php
namespace App\Services;

use App\Models\Image;


class ImageService
{
	function __construct()
	{

	}


	/**
	 * Given either an array of product or just a single product
	 * compile an array of all images.
	 *
	 * @param	mixed	$product
	 * @return	array
	 */
	public static function getParentImages($products)
	{
		if(is_array($products))
		{
			$images = array();
			foreach($products as $product)
			{
				$images = array_merge($images, self::getImages($product));
			}
			return $images;
		}
		else
		{
			return self::getImages($products);
		}
	}



	/**
	 * Given the product, get all images.
	 * - parent images and thumbnails.
	 *
	 * @param	App\Models\Product	$product
	 * @return	array
	 */
	public static function getImages($product)
	{
		$images = array();
		$mapping = $product->images;
		if(sizeof($mapping)>0)
		{
			foreach($mapping as $m)
			{
				array_push($images,$m);
				$pp_images = Image::where('image_parent_id',$m->id)->get();
				foreach($pp_images as $pi)
				{
					array_push($images,$pi);
				}
			}
		}
		return $images;
	}



	public static function getThumbnails($product)
	{
		$thumbnails = array();
		$mapping = $product->images;
		if(sizeof($mapping)>0)
		{
			foreach($mapping as $m)
			{
				$images = Image::where('image_parent_id',$m->id)->get();
				array_push($thumbnails, $images);
			}
		}
		return $thumbnails;
	}



	public static function getMainImage($product)
	{
		$main_image_folder_name = $this->getStoragePath($product->id);
		$main_image_file_name = $id."-1.jpeg";
		if(sizeof($images)==1)
		{
			$main_image_file_name = $images[0]->image_file_name;
		}
		return array('name'=>$main_image_file_name, 'folder'=>$main_image_folder_name);
	}





	/**
	 * Construct a media image storage path from the ID given and return string
	 *
	 * @param   string  $str_id String holding the ID value as a number
	 * @return  string
	 */
	protected function getStoragePath($str_id)
	{
#		$this->LogFunction("getStoragePath()");
		$id = "$str_id";
		$path="/media";
		for($i=0;$i<strlen($id);$i++)
		{
			$path.="/".$id[$i];
#			$this->LogMsg($path);
		}
#		$this->LogMsg("Path is [ $path ] ");
		return $path;
	}


}
