<?php
/**
 * \class AdvertRequest
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-08-01
 */
namespace App\Http\Requests;


use App\Http\Requests\Request;


/**
 * \brief Validation request class for the Advert form.
 */
class AdvertRequest extends Request
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
			'advert_name'=>'required|min:4|max:255',
			'advert_html_code'=>'required|min:4|max:255'
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
			'advert_name.required'=>'You must enter a name',
			'advert_name.min'=>'Name must be at least 4 characters in length',
			'advert_name.max'=>'Name must be no more than 255 characters in length',
			'advert_html_code.required'=>'You must enter some HTML code',
			'advert_html_code.min'=>'HTML Code field must be at least 4 characters in length',
			'advert_html_code.max'=>'HTML Code field must be no more than 2048 characters in length'
		];
	}
}
