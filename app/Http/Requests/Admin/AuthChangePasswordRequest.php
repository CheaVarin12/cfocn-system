<?php

namespace App\Http\Requests\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class AuthChangePasswordRequest extends FormRequest
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
            'password' => 'required|min:6',
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
            'confirm_password' => 'required|same:password|min:6',
        ];
    }
    public function messages()
    {
        return [
            'current_password.required' => "Current password is required",
            'password.required' => "Password is required",
            'password.min'=> 'Password should be minimum of 6 character',
            'confirm_password.same' => "The confirm password not match confirm password",
            'confirm_password.required' => "Confirm password is required",
            'confirm_password.min'=> 'Confirm password should be minimum of 6 character',
        ];
    }
}
