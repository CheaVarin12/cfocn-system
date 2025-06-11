<?php

namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InvoiceCreateReceiptRequest extends FormRequest
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
            'receipt_number' =>  'required|unique:receipts,receipt_number,' . $acceptedId,
            'issue_date' =>  'required',
            'note'  => 'required',
            'status_type' => 'required|in:"invoice"',
        ];
    }
    public function messages()
    {
        return [
            'receipt_number.required' => "Receipt number is required",
            'receipt_number.unique' => "Receipt number ready to use pls enter other number.",
            'issue_date.required' => "Issue date is required",
            'note.required' => 'Note is required',
            'status_type.required' => "Status type is required",
            'status_type.in' => "Status type is invalid format",
        ];
    }
}
