<?php

namespace App\Http\Requests\Admin;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DMCSubmitMultipleRequest extends FormRequest
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
            'ids' => 'required',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages()
    {
        return [
            'ids.required' => "Please select invoices",
        ];
    }

    /**
     * Add additional validation after base rules.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $ids = $this->input('ids', []);

            if (is_array($ids) && count($ids)) {
                $alreadySent = Invoice::whereIn('id', $ids)
                    ->where('doc_status', 'is_send')
                    ->pluck('invoice_number')
                    ->toArray();

                if (!empty($alreadySent)) {
                    $validator->errors()->add('ids', 'The following invoices were already sent: ' . implode(', ', $alreadySent));
                }
            }
        });
    }
}
