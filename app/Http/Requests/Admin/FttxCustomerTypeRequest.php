<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FttxCustomerTypeRequest extends FormRequest
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
            'name'                       => 'required',
            'status'                     => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required'                        => "Name is required",
            'status.required'                      => "Status is required",
        ];
    }
}
