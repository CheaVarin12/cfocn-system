<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
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
            'po_number'              => 'required|unique:purchase_orders,po_number,'.$acceptedId,
            'customer_id'            => 'required',
            'dataTable'              => 'required',
            'project_id'             => 'required',
            'type'                   => 'required',
            'type_id'                => 'required',
            'issue_date'             => 'required',
            'duration'               => 'required',
            'end_date'               => 'required',
            'location'               => 'required',   
        ];
    }

    public function messages()
    {
        return [
            'po_number.required'        => "Po number is required",
            'customer_id.required'      => "Customer is required",
            'dataTable.required'        => "From is required",
            'project_id.required'       => "Project is required",
            'type_id.required'          => "Service type is required",
            'type.required'             => "Po Service type is required",
            'issue_date.required'       => "Issue date is required",
            'duration.required'         => "Duration is required",
            'end_date.required'         => "End date is required",
            'location.required'         => "Location is required",
        ];
    }
}
