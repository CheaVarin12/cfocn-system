<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderCreditNoteRequest extends FormRequest
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
            'credit_note_number' =>  'required|unique:work_order_credit_notes,credit_note_number,' . $acceptedId,
            'issue_date' => 'required',
            'exchange_rate' => 'required',
            'note' => 'required',
            'credit_note_details' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'credit_note_number.required' => "Credit note number is required",
            'credit_note_number.unique' => "Credit note number use ready pls enter other number.",
            'issue_date.required'   => "Issue date is required",
            'exchange_rate.required' => "Exchange rate is required",
            "note.required" => "Note is required",
            "credit_note_details.required" => "Credit Note details is required",
        ];
    }
}
