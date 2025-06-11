<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CloseDateRequest extends FormRequest
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
            'dateValid' => 'required|unique:close_dates,date,' . $acceptedId,
            'status' => 'bail|required|max:2|numeric|in:1,2',
        ];
    }
    public function messages()
    {
        return [
            'dateValid.required' => "Date is required",
            'dateValid.unique' => "Date already exists.",
            'status.required' => "Status is required",
            'status.numeric' => "Status is number",
            'status.in' => "Status is invalid format",
        ];
    }

}
