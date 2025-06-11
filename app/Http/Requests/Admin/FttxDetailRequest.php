<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FttxDetailRequest extends FormRequest
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

    public function rules()
    {
        $acceptedId = $this->id ?? '';
        return [
            'date'                       => 'required',
            'expiry_date'                => 'required',
        ];
    }
    public function messages()
    {
        return [
            'date.required'                        => "Date is required",
            'expiry_date.required'                 => "Expiry date is required",
        ];
    }
}
