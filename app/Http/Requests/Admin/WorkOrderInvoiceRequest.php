<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkOrderInvoiceRequest extends FormRequest
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
        $data = Order::find($this->order_id);
        return [
            'customer_id'       => 'required',
            'order_id'          => 'required',
            'invoice_number'    =>  'required|unique:work_order_invoices,invoice_number,'.$acceptedId,
            'exchange_rate'     => 'required',
            'issue_date'        => 'required',
            'note'              => 'required',
            'order_details'     => 'required',
            //'install_number'    => ($data->type_id == 2 ? 'required' : 'nullable') . '|numeric',
            //'charge_number'     => ($data->type_id == 2 ? 'required' : 'nullable') . '|numeric',
        ];
    }
    public function messages()
    {
        return [
            'customer_id.required'      => 'Customer is required',
            'order_id.required'         => 'Order is required',
            'invoice_number.required'   => "Invoice number is required",
            'invoice_number.unique'     => "Invoice number ready to use pls enter other invoice_number.",
            'exchange_rate.required'    => "Exchange rate is required",
            'issue_date.required'       => "Issue date is required",
            "note.required"             => "Note is required",
            "order_details.required"    => "Order details is required",
            "install_number.required"   => "Install number is required",
            "charge_number.required"    => "Charge number is required",
            "install_number.numeric"    => "Install number in valid format",
            "charge_number.numeric"     => "Charge number in valid format",
        ];
    }
}
