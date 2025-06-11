<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FttxCustomerPriceRequest extends FormRequest
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
            'customer_id'     => 'required|unique:fttx_customer_prices,customer_id,' . $acceptedId,
            'status'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => "Isp is required",
            'customer_id.unique' => "The isp has already been taken.",
        ];
    }
}
