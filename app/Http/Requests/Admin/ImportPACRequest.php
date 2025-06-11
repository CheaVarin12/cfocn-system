<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportPACRequest extends FormRequest
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
            'pac_file' => 'required|mimes:xlsx,xls',
        ];
    }

    public function messages()
    {
        return [
            'pac_file.required' => 'PAC file is required',
            'pac_file.mimes' => 'PAC file wrong extension',
        ];
    }
}
