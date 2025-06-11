<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomerRequest extends FormRequest
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
        $typeValue = $this->type;
        $acceptedId = $this->id ?? '';
    
        return [
            'customer_code' => 'required|unique:customers,customer_code,' . $acceptedId ,
            'vat_tin' => $typeValue == 3 ? 'nullable' : 'nullable|unique:customers,vat_tin,' . $acceptedId,
            'type' => 'required',
        ];
    }
    
    public function messages()
    {
        return [
            'customer_code.required' => "Customer code is required",
            'customer_code.unique'   => "Customer code use ready pls enter other code.",
            'vat_tin.unique'         => "Vat tin ready to use pls enter other.",
            'type'                   => 'Customer type is required',
        ];
    }
}
