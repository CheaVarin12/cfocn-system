<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class WorkOrderReceiptEditStatusRequest extends FormRequest
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
        return [
            'payment_method' => 'required|in:"bank","cash","cheque"',
            'payment_status' => 'required|in:"portal","paid"',
            'paid_date' => 'required',
            'payment_des' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'payment_method.required' => "Payment method is required",
            'payment_method.in' => "Payment method is invalid format",
            'payment_status.required' => "Payment status is required",
            'payment_status.in' => "Payment status is invalid format",
            'paid_date.required' => "Paid date is required",
            'payment_des.required' => "Description is required"
        ];
    }
}
