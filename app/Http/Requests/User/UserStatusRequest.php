<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class UserStatusRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        return [
            'is_email_verified' => 'required|in:' . array_to_string(email_status()),
            'is_active' => 'required|in:' . array_to_string(account_status()),
            'is_financial_active' => 'required|in:' . array_to_string(financial_status()),
            'is_accessible_under_maintenance' => 'required|in:' . array_to_string(maintenance_accessible_status()),
        ];
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-06 2:49 PM
     * @description:
     * @return array
     */
    public function attributes()
    {
        return [
            'is_email_verified' => __('Email Status'),
            'is_active' => __('Account Status'),
            'is_financial_active' => __('Financial Status'),
            'is_accessible_under_maintenance' => __('Maintenance Access Status'),
        ];
    }
}
