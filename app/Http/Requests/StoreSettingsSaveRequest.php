<?php
/**
 * @date 2017-01-03
 * @author Sid Young <sid@off-grid-engineering.com>
 */
namespace App\Http\Requests;



use App\Http\Requests\Request;


/**
 * \brief	MVC Request Validation class for the settings dialogs
 */
class StoreSettingsSaveRequest extends Request
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
			'setting_name'=>'required|min:3',
			'setting_value'=>'required|min:1'
        ];
    }


    /**
     * Return the messages for the rules
     *
     * @return array
     */
	public function messages()
	{
		return [
		];
	}
}
