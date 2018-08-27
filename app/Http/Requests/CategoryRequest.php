<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CategoryRequest extends Request
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
			"category_title"=>"required|min:4|max:128",
			"category_description" => "required|min:7|max:256",
			"category_url" => "present",
			"category_parent_id" => "required|integer",
			"category_status" => "required|alpha"
        ];
    }
}
