<?php
/**
 * \class	ProductRequest
 * \version	1.0.4
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
 */
namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\CategoryProduct;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\AttributeService;
use Illuminate\Foundation\Http\FormRequest;


/**
 * \brief Custom request class to process form data and
 * provide common methods for processing Product data.
 */
class ProductRequest extends FormRequest
{
	/**
	 * Save the newly created product and return its ID
	 *
	 * @return	integer
	 */
	public function SaveProduct()
	{
		$product_id = ProductService::insert($this);
		if($product_id > 0)
		{
			CategoryService::AssignCategories($this, $product_id);
		}
		return $product_id;
	}


	/**
	 * Save the product attributes for the given product ID
	 *
	 * @param	integer	$product_id
	 * @return	void
	 */
	public function SaveProductAttributes($product_id)
	{
		$empty = array();
		$attributes = $this->input('attributes', $empty );
		if(sizeof($attributes)>0)
		{
			AttributeService::AssignAttributes($attributes,$product_id);
		}
	}


	/**
	 * Update our existing record
	 *
	 * @param	integer	$product_id
	 * @return	integer
	 */
	public function UpdateProduct($product_id)
	{
		$this->request->add(['id', $product_id]);
		$count = ProductService::update($this);
		CategoryService::AssignCategories($this, $product_id);
		return $count;
	}



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
			'prod_weight'=>'numeric',
			'prod_qty'=>'numeric',
			'prod_reorder_qty'=>'numeric',
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
