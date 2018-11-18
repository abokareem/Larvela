<?php
/**
 * \version	1.0.5
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
 * ProductImageHandling - Code for handling image processing in Product Controllers classes.
 */
namespace App\Traits;

use Input;

use App\Models\Image;
use App\Models\ProdImageMap;

use App\Jobs\ResizeImages;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Http\Requests\ProductRequest;


/**
 * \brief Code for handling image processing in Product Controllers classes.
 */
trait ProductImageHandling
{
protected $MEDIADIR= '/media';



	/**
	 * Save the image(s) thats been uploaded for this product
	 *
	 *
	 * @return void
	 */
	public function SaveImages(ProductRequest $request, $id)
	{
		$this->LogFunction("SaveImages(".$id.")");
		$good_extensions = array("png","jpg","jpeg");
		$file_count = 0;
		$file_name = "N/A";
		$files = $request->file('images');
		if($request->hasFile('images'))
		{
			$this->LogMsg("Processing File Data");
			$files = $request->file('images');
			$file_count = sizeof($files);
			foreach($files as $file)
			{
				$this->LogMsg( print_r($file,1) );
				$file_type = explode("/",$file->getClientMimeType());
				$file_extension = strtolower($file_type[1]);	#image/jpeg
				$file_name = $file->getClientOriginalName();
				
				$file_path = $this->getStoragePath($id);
				$sub_path = $this->getStorageSubPath($id);

				$this->LogMsg("Path: [".$file_path."]");
				$this->LogMsg("File name [".$file_name."]");

				#if($file_type[0]=="image")
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

				$id_name = $id."-".$image_index.".".$file_extension;
				$this->LogMsg("New file ID [".$id_name."]");
				$this->LogMsg("File Path   [".$file_path."]");

				$file->move($file_path,$id_name);

				$newname = $file_path."/".$id_name;
				$this->LogMsg("New File name [".$newname."]");

				list($width, $height, $type, $attr) = getimagesize($newname);
				$size = filesize($newname);
				$o = new Image;
				$o->image_file_name = $id_name;
				$o->image_folder_name = $sub_path;
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
		}
		else
		{
			$this->LogError("No Images.");
			\Session::flash('flash_error',"ERROR (image) - No images present!");
		}
		$this->LogMsg("function Done");
	}


	

	/**
	 * Create the path needed to store product images and return the full filesystem path to place file.
	 *
	 * @pre		none
	 * @post	creates directory structure as needed
	 *
	 * @param	integer	$id - the product ID
	 * @return	string 
	 */
	public function XXXgetStoragePath( $id )
	{
		$this->LogFunction("getStoragePath(".$id.")");
		$path="";
		$length = strlen($id);
		$id = "".$id."";
		for($i=0 ; $i < $length ; $i++)
		{
			$path.="/".$id[$i];
		}
		$finalpath = public_path()."/media".$path;
		if(is_dir($finalpath))
		{
			$this->LogMsg("PATH [".$finalpath."]");
			return $finalpath;
		}
		else
		{	
			$this->LogMsg("Create Path [".$finalpath."]");
			try { mkdir($finalpath,0775,true); }
			catch(Exception $e)
			{
				$this->LogError("Failed to create Path [".$finalpath."]");
			}
		}
		$this->LogMsg("PATH [".$finalpath."]");
		return $finalpath;
	}



	/**
	 * Create the subpath needed to store product images and return a partial filesystem path.
	 *
	 * @param	integer	$id - the product ID
	 * @return	string
	 */
	public function XXXgetStorageSubPath($id)
	{
		$this->LogFunction("getStorageSubPath(".$id.")");

		$path="media";
		$length = strlen($id);
		$id = "".$id."";
		for($i=0 ; $i < $length ; $i++)
		{
			$path.="/".$id[$i];
		}
		$this->LogMsg("PATH [".$path."]");
		return $path;
	}

}
