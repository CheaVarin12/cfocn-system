<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EditCustomerDMCRequest extends FormRequest
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
        $customer_id = request('dmc_customer_id') ?? null;
        return [
            'customer_code'     => 'required',
            'customer_name'     => 'required',
            'po_number'         => 'required',
            'pac_number'        => 'required',
            'service_type'      => 'required',
            'type'              => 'required',
            'qty_cores'         => 'required',
            'length'            => 'required',
            'customer_address'  => 'required',
            'location'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'register_date.required'    => 'Register date is required',
            'customer_code.required'    => 'Customer code is required',
            'customer_code.unique'      => 'Customer code is already taken',
            'customer_name.required'    => 'Customer name is required',
            'po_number.required'        => 'PO number is required',
            'pac_number.required'       => 'PAC number is required',
            'service_type.required'     => 'Service type is required',
            'type.required'             => 'Type is required',
            'qty_cores.required'        => 'Qty is required',
            'length.required'           => 'Length is required',
            'customer_address.required' => 'Customer Address is required',
            'location'                  => 'Location is required',
        ];
    }
}
