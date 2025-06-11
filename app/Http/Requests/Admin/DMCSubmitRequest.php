<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DMCSubmitRequest extends FormRequest
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
        return [
            // 'file' =>  'required',
            'invoice_id' =>  'required|numeric',
        ];
    }
    public function messages()
    {
        return [
            // 'file.required' => "file is required",
            'invoice_id.required' => "Invoice id is required",
            "invoice_id.numeric" => "Invoice id in valid format",
        ];
    }
}
