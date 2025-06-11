<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FttxRenewalRequest extends FormRequest
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
            'deadline'                  => 'required',
            'number_of_month'           => 'required',
            'new_deadline'              => 'required',
        ];
    }
    public function messages()
    {
        return [
            'deadline.required'                    => "Current deadline is required",
            'number_of_month.required'             => "Number of month is required",
            'new_deadline.required'                => "new_deadline is required",
        ];
    }
}
