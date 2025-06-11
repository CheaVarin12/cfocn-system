<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportCustomerDMCRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'project_id' => 'required',
            'customer_file' => 'required|mimes:xlsx,xls',
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'Please select project',
            'customer_file.required' => 'Customer file is required',
            'customer_file.mimes' => 'Customer file wrong extension',
        ];
    }
}
