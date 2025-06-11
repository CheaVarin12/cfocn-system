<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportFttxRequest extends FormRequest
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
            'fttx_file' => 'required|mimes:xlsx,xls',
        ];
    }

    public function messages()
    {
        return [
            'fttx_file.required' => 'Fttx file is required',
            'fttx_file.mimes' => 'Fttx file wrong extension',
        ];
    }
}
