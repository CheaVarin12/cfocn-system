<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;

class CreditNoteRequest extends FormRequest
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
        $typeId = $this->type_id ?? '';
        return [
            'credit_note_number' =>  'required|unique:credit_notes,credit_note_number,' . $acceptedId,
            'invoice_number' =>  'required',
            'issue_date' => 'required',
            'customer_id'   => 'required',
            'project_id'   => 'required',
            'exchange_rate' => 'required',
            'note'  => 'required',
            'purchase_details' => 'required',
            'install_number' => ($typeId == 2 ? 'required' : 'nullable') . '|numeric',
            'charge_number' => ($typeId == 2 ? 'required' : 'nullable') . '|numeric',
        ];
    }
    public function messages()
    {
        return [
            'credit_note_number.required' => "Credit note number is required",
            'credit_note_number.unique' => "Credit note number use ready pls enter other number.",
            'invoice_number.required' => "Invoice number is required",
            'invoice_number.unique' => "Invoice number ready to use pls enter other number.",
            'issue_date.required'   => "Issue date is required",
            'customer_id.required'  => "Customer is required",
            'project_id.required'  => "Project is required",
            'type_id.required'  => "Type is required",
            'charge_type.required' => "Charge type is required",
            'exchange_rate.required' => "Exchange rate is required",
            "note.required" => "Note is required",
            "purchase_details.required" => "Purchase details is required",
            "install_number.required" => "Install number is required",
            "charge_number.required" => "Charge number is required",
            "install_number.numeric" => "Install number in valid format",
            "charge_number.numeric" => "Charge number in valid format",
        ];
    }
}
