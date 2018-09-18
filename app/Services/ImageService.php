<?php
/**
 * \class	ImageService
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-09-17
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
 */
namespace App\Services;

use App\Models\Image;

/**
 * \brief This service rovides a range of commonly used image retrieval methods.
 */
class ImageService
{


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
	 * - parent images only!
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
				#$pp_images = Image::where('image_parent_id',$m->id)->get();
				#foreach($pp_images as $pi)
				#{
				#	array_push($images,$pi);
				#}
			}
		}
		return $images;
	}



	/**
	 * Return an array of thumbnails of all the main product images.
	 * - Thumbnails are usually in 3 sizes and have a parent ID that is not zero.
	 * - Parent images are defined in pivot table "image_product"
	 *
	 * @return	array
	 */
	public static function getThumbnails($product)
	{
		$thumbnails = array();
		$mapping = $product->images;
		if(sizeof($mapping)>0)
		{
			foreach($mapping as $m)
			{
				$images = Image::where('image_parent_id',$m->id)->where('image_height',68)->get();
				if(!is_null($images))
				{
					foreach($images as $img)
					{
						array_push($thumbnails, $img);
					}
				}
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
