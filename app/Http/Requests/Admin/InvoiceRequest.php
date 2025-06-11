<?php

namespace App\Http\Requests\Admin;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
        $data = Purchase::find($this->po_id);
        $type=null;
        $checkMultiple = false;
        if($this->check_multiple_pac != null){
            $type = $this->type_id;
            $checkMultiple = true;
        }else{
            $type = $data->type_id;
        }
        return [
            'customer_id'       => 'required',
            'po_id'             => 'required',
            'invoice_number'    => 'required|unique:invoices,invoice_number,'.$acceptedId,
            'exchange_rate'     => 'required',
            'issue_date'        => 'required',
            'note'              => 'required',
            'purchase_details'  => 'required',
            'install_number'    => ($type == 2 ? 'required' : 'nullable') . '|numeric',
            'charge_number'     => ($type == 2 ? 'required' : 'nullable') . '|numeric',
            'project_id'        =>  $checkMultiple ? 'required' : 'nullable',
            'service_type'      =>  $checkMultiple ? 'required' : 'nullable',
            'multiple_po_id'    =>  $checkMultiple ? 'required' : 'nullable',
            'tax_status'        => 'required',
        ];
    }
    public function messages()
    {
        return [
            'invoice_number.required'       => "Invoice number is required",
            'invoice_number.unique'         => "Invoice number ready to use pls enter other invoice_number.",
            'exchange_rate.required'        => "Exchange rate is required",
            'issue_date.required'           => "Issue date is required",
            "note.required"                 => "Note is required",
            "purchase_details.required"     => "Purchase details is required",
            "install_number.required"       => "Install number is required",
            "charge_number.required"        => "Charge number is required",
            "install_number.numeric"        => "Install number in valid format",
            "charge_number.numeric"         => "Charge number in valid format",
            "customer_id.required"          => "Customer is required",
            "project_id.required"           => "Project is required",
            "service_type.required"         => "Service type is required",
            "multiple_po_id.required"       => "Pac is required",
            "tax_status.required"           => "Tax status required",
        ];
    }
}
