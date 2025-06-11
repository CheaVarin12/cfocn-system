<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FttxRequest extends FormRequest
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
            'work_order_isp'            => 'required',
            'customer_id'               => 'required',
            'work_order_cfocn'          => 'required',
            'status'                    => 'required',
            'pos_speed_id'              => 'required',
            'team_install'              => 'required',
            'completed_time'            => 'required',
            'first_payment_period'      => 'required',
            'start_payment_date'        => 'required',
            'deadline'                  => 'required',
        ];
    }
    public function messages()
    {
        return [
            'work_order_isp.required'                   => "Work order isp is required",
            'customer_id.required'                      => "Isp is required",
            'work_order_cfocn.required'                 => "Work order cfocn is required",
            'work_order_cfocn.unique'                   => "Work order cfocn is already taken",
            'subscriber_no.required'                    => "Subscriber no is required",
            'status.required'                           => "Status is required",
            'name.required'                             => "Name is required",
            'phone.required'                            => "Phone is required",
            'pos_speed_id.required'                     => "Pos speed is required",
            'applicant_team_install.required'           => "Applicant team install is required",
            'team_install.required'                     => "Team install is required",
            'create_time.required'                      => "Create time is required",
            'completed_time.required'                   => "Completed time is required",
            'start_payment_date.required'               => "Last payment date is required",
            'last_payment_date.required'                => "Last payment date is required",
            'deadline.required'                         => "Deadline is required",
            'month.required'                            => "Month is required",
            'day.required'                              => "Day is required",
            'day_remaining.required'                    => "Day remaining is required",
            'customer_type.required'                    => "Customer type is required",
            'new_installation_fee.required'             => "New installation fee is required",
            'first_payment_period'                      => "First payment period is required",
            'initial_payment_period'                    => "Initial payment period is required",
            'rental_price'                              => "Rental price is required",
        ];
    }
}
