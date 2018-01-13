<?php
/**
 * \class ImageService
 */
namespace App\Services;

use App\Http\Requests\ImageRequest;

use App\Models\Image;


/**
 * \brief Service layer for the Image Model
 */
class ImageService
{

	public static function insert(ImageRequest $request)
	{
		$rv = 0;
		$o = new Image;
		$o->image_file_name = $request['image_file_name'];
        $o->image_folder_name = $request['image_folder_name'];
        $o->image_size = $request['image_size'];
        $o->image_height = $request['image_height'];
        $o->image_width = $request['image_width'];
        $o->image_order = $request['image_order'];
        $o->image_parent_id = $request['image_parent_id'];

		if(($rv=$o->save()) > 0)
		{
			\Session::flash('flash_message','Image Saved!');
		}
		else
		{
			\Session::flash('flash_error','Image Save Failed!');
		}
		return $rv;
	}



	public static function update(ImageRequest $request)
	{
		$rv = 0;
		$o = Image::find($request['id']);
		$o->image_file_name = $request['image_file_name'];
        $o->image_folder_name = $request['image_folder_name'];
        $o->image_size = $request['image_size'];
        $o->image_height = $request['image_height'];
        $o->image_width = $request['image_width'];
        $o->image_order = $request['image_order'];
        $o->image_parent_id = $request['image_parent_id'];

		if(($rv=$o->save()) > 0)
		{
			\Session::flash('flash_message','Image updated!');
		}
		else
		{
			\Session::flash('flash_error','Image update Failed!');
		}
		return $rv;
	}
}
