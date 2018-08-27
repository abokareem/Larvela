<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProductRequest extends Request
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
			'prod_sku'=>'required|min:4|max:32',
			'prod_title'=>'required|min:4|max:128',
			'prod_short_desc'=>'required|min:8|max:1024',
			'prod_long_desc'=>'required|min:8|max:8192',
			'prod_weight'=>'required|numeric',
			'prod_qty'=>'required|numeric',
			'prod_reorder_qty'=>'required|numeric',
			'prod_base_cost'=>'required|numeric',
			'prod_retail_cost'=>'required|numeric'
        ];
    }


	public function messages()
	{
		return [
			'prod_sku.required'=>'You must enter an SKU',
			'prod_title.required'=>'You must enter a Title',
			'prod_title.min'=>'Title must be at least 4 characters in length',
			'prod_title.max'=>'Title must be no more than 128 characters in length',
			'prod_short_desc.required'=>'Short Description cannot be blank',
			'prod_short_desc.min'=>'Short Description must be at least 8 characters in length',
			'prod_short_desc.max'=>'Short Description must be no more than 8192 characters in length',
			'prod_long_desc.required'=>'Long Description cannot be blank'
		];
	}
}
