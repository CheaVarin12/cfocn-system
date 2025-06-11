<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends FormRequest
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
        $acceptedId = $this->id ?? '';
        return [
            'pac_number'    =>  'required',
            'po_number'     => 'required',
            // 'cores'         => 'required',
            'customer_id'   => 'required',
            'dataTable'     => 'required',
            'issue_date'    => 'required',
            // 'length'        => 'required',
            // 'pac_type'      => 'required',
            'project_id'    => 'required',
            'type_id'       => 'required',
            'location'      => 'required',
        ];
    }
    public function messages()
    {
        return [
            'pac_number.required'   => "Pac number is required",
            'po_number.required'    => "Po number is required",
            'cores.required'        => "Cores is required",
            'customer_id.required'  => "Customer is required",
            'issue_date.required'   => "Issue date is required",
            'dataTable.required'    => "From is required",
            'length.required'       => "Length is required",
            'pac_type.required'     => "Pac_type is required",
            'project_id.required'   => "Project is required",
            'type_id.required'      => "Type is required",
            'location.required'     => "Location is required",
        ];
    }
}
