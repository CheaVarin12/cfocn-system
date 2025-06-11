<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FttxPosSpeedRequest extends FormRequest
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
            'split_pos'                  => 'required',
            'rental_price'               => 'required',
            'ppcc_price'                 => 'required',
            'new_install_price'          => 'required',
            'status'                     => 'required',

        ];
    }
    public function messages()
    {
        return [
            'split_pos.required'                   => "Split (POS) is required",
            'rental_price.required'                => "Rental price is required",
            'ppcc_price.required'                  => "PPCC price is required",
            'new_install_price.required'           => "New install price is required",
            'status.required'                      => "Status is required",
        ];
    }
}
