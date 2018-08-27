<?php
/**
 * \class SeoRequest
 *
 */
namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * \breif Validation class for the SEO entry form
 */
class SeoRequest extends Request
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
			'seo_token'=>'required|min:4|max:255',
			'seo_html_data'=>'required',
			'seo_status'=>'required'
        ];
    }


    /**
     * return suitable messages
     *
     * @return array
     */
	public function messages()
	{
		return [
			'seo_token.required'=>'You must enter a token name > 4 and less than 255',
			'seo_html_data.required'=>'Field is required'
		];
	}
}
