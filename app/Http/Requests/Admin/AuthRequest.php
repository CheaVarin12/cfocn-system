<?php

namespace App\Http\Requests\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'username' => 'required|max:250',
            'email'     => 'required|max:100|email|unique:users,email,'.$acceptedId,
            'phone' =>  'nullable|digits_between:9,15|numeric|unique:users,phone,'.$acceptedId
        ];
    }
    public function messages()
    {
        return [
            'username.required' => "User name is required",
            'email.required' => "Email is required",
            'email.unique' => "The email has already been taken.",
            'email.max' => "Email max 100 character",
            'phone.required' => "Phone is required",
            'phone.unique' => "The phone has already been taken.",
            'phone.digits_between' => "The phone digits between 9 to 15 character.",
            'password.required' => "Password is required",
            'password.same' => "The password not match confirm password",
            'confirm_password.required' => "Confirm Password is required",
        ];
    }
}
