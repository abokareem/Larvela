<?php
/**
 * \class ImageRequest
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-08-01
 */
namespace App\Http\Requests;


use App\Http\Requests\Request;

/**
 * \brief  Validation class from the image upload form of the product edit/add form
 */
class ImageRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'image_file_name'=>'required|min:4|max:255',
        ];
    }



	/**
	 * Messages to return for each type of validation error.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'image_file_name.required'=>'You must enter a name'
		];
	}
}
