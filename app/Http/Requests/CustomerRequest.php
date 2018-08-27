<?php
/**
 * \class CustomerRequest
 * @author Sid Young <sid@off-grid-engineering.com>
 * @date 2016-08-01
 */
namespace App\Http\Requests;



use App\Http\Requests\Request;


/**
 * \brief Validation class for validating the customer form
 */
class CustomerRequest extends Request
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
			'customer_name'=>'required|min:4|max:255',
			'customer_email'=>'required|email',
			'customer_status'=>'required',
			'customer_store_id'=>'required|numeric',
			'customer_source_id'=>'required|numeric'
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
