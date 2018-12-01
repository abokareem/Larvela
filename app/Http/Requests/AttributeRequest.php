<?php namespace App\Http\Requests;

/**
 * \class AttributeRequest
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2017-01-02
 */

use App\Http\Requests\Request;


/**
 * Validation class for validating the customer form
 */
class AttributeRequest extends Request
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
			'attribute_name'=>'required|min:4',
			'attribute_token'=>'required|min:3'
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
		];
	}
}
