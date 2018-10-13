<?php
/**
 * Larvela
 * Copyright (C) 2017
 * by Present & Future Holdings Pty Ltd Trading as Off Grid Engineering
 * https://off-grid-engineering.com
 *
 *
 * \addtogroup Internal
 * ProductImageHandling - Code for handling image processing in Product Controllers classes.
 */
namespace App\Traits;

use Input;

use App\Models\Image;
use App\Models\ProdImageMap;

use App\Jobs\ResizeImages;
use Illuminate\Contracts\Bus\Dispatcher;


/**
 * \brief Code for handling image processing in Product Controllers classes.
 */
trait ProductImageHandling
{
protected $MEDIADIR= '/media';



	/**
	 * Save the image thats been uploaded for this product
	 *
	 * TODO - more work needed on this.
	 *
	 * @return void
	 */
	public function SaveUploadedImage($id)
	{
		$this->LogFunction("SaveUploadedImage(".$id.")");

		$file_data = Input::file('file');
		if($file_data)
		{
			$this->LogMsg("Processing File Data");
			$file_type = explode("/",$file_data->getClientMimeType());
			$text = "File Data ".print_r($file_type,true);
			$this->LogMsg($text);
			if($file_type[0]=="image")
			{
				$extension = $file_type[1];
				$filename = $file_data->getClientOriginalName();
				$subpath = $this->getStorageSubPath($id);
				$filepath = $this->getStoragePath($id);
				#
				# if no images mapped then parent image is "product_id"-"image_order"."extension"
				#        otherwise, access prod_image_maps and determine the order of images, so increment image order.
				#
				$base_images = ProdImageMap::where('product_id',$id)->get();
				$image_index = 1;
				if(sizeof($base_images)>0)
				{
					#
					# fetch all image db records and parse the names for the indexes already assigned.
					#
					# testing
					$this->LogMsg("Process each image.");
					foreach($base_images as $bi)
					{
						$text = "IDX [".$image_index."]";
						$this->LogMsg($text);
						$text = "DATA: ".print_r($bi,true);
						$this->LogMsg($text);
						$img = Image::where('id',$bi->image_id)->first();
						$file_name = explode(".",$img->image_file_name);
						$f_n_parts = explode("-",$file_name[0]);
						$text = "DATA ".print_r($f_n_parts, true);
						$this->LogMsg( $text );
						if($f_n_parts[1] == $image_index)
						{
							$this->LogMsg("Increment image index!");
							$image_index++;
						}
						if($f_n_parts[1] > $image_index)
						{
							$this->LogMsg("Increment image sequence!");
							$image_index = $f_n_parts[1]+1;
						}
					}
				}

				$id_name = $id."-".$image_index.".".$extension;
				$text = "New ID [".$id_name."]";
				$this->LogMsg($text);

				$file_data->move($filepath,$id_name);
				$newname = $filepath."/".$id_name;
				$text = "New File name [".$newname."]";
				$this->LogMsg($text);

				list($width, $height, $type, $attr) = getimagesize($newname);
				$size = filesize($newname);
				$o = new Image;
				$o->image_file_name = $id_name;
				$o->image_folder_name = $subpath;
				$o->image_size = $size;
				$o->image_height = $height;
				$o->image_width = $width;
				$o->image_parent_id = 0;
				$o->image_order = 0;

				$this->LogMsg("Create Image Entry");
				$o->save();
				$iid = $o->id;
				$text = "New Image ID [".$iid."]";
				$this->LogMsg($text);
				#
				# Use Eloquent to insert into Pivot table
				#
				$image = Image::find($iid);
				$image->products()->attach($id);

				$this->LogMsg("Dispatch resize job");
				dispatch( new ResizeImages($id, $iid) );
				$this->LogMsg("Back in Controller");
			}
			else
			{
				$this->LogError("Invalid file type.");
				\Session::flash('flash_error',"ERROR - Only images are allowed!");
			}
		}
		$this->LogMsg("function Done");
	}
}
