<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReceiptRequest extends FormRequest
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
            'customer_id'   => 'required|numeric',
            'type_id'   => 'required|numeric',
            'issue_date' =>  'required',
            'note'  => 'required',
            'status_type' => 'required|in:"receipt","invoice"',
        ];
    }
    public function messages()
    {
        return [
            'receipt_number.required' => "Receipt number is required",
            'receipt_number.unique' => "Receipt number ready to use pls enter other number.",
            'customer_id.required' => "Customer is required",
            'customer_id.numeric' => "Customer is invalid format",
            'type_id.required' => "Service type is required",
            'type_id.numeric' => "Service type is invalid format",
            'issue_date.required' => "Issue date is required",
            'note.required' => 'Note is required',
            'status_type.required' => "Status type is required",
            'status_type.in' => "Status type is invalid format",
        ];
    }
}
