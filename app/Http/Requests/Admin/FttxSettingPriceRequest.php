<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FttxSettingPriceRequest extends FormRequest
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
        $acceptedId = $this->id ?? '';
        return [
            'price'                      => 'required',
            'type'                       => 'required',
            'status'                     => 'required',
        ];
    }
    public function messages()
    {
        return [
            'price.required'                       => "Price is required",
            'type.required'                        => "Type is required",
            'status.required'                      => "Status is required",
        ];
    }
}
