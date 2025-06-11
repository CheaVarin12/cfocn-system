<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $acceptedId = $this->id ?? '';

        return [
            'order_number'  => 'required',
            'customer_id'   => 'required',
            'project_id'    => 'required',
            'dataTable'     => 'required',
            //'type_id'       => 'required'
        ];
    }
    public function messages()
    {
        return [
            'order_number.required' => "Order number is required",
            'customer_id.required'  => "Customer is required",
            'project_id.required'   => "Project is required",
            'dataTable.required'    => "From is required",
            //'type_id.required'      => "Type is required"
        ];
    }
}
