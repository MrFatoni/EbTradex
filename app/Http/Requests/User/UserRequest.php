<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UserRequest extends Request
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
        $rules = [
            "first_name" => "required|alpha_space|between:2,255",
            "last_name" => "required|alpha_space|between:2,255",
            "address" => "max:500",
        ];

        if($this->isMethod('POST')){
            $rules["user_role_management_id"] = "required|exists:user_role_managements,id";
            $rules["email"] = "required|email|unique:users,email|between:5,255";
            $rules["username"] = "required|unique:users,username|max:255";
            $rules["is_email_verified"] = "required|in:" . array_to_string(email_status());
            $rules["is_financial_active"] = "required|in:" . array_to_string(financial_status());
            $rules["is_active"] = "required|in:" . array_to_string(account_status());
            $rules["is_accessible_under_maintenance"] = "required|in:" . array_to_string(maintenance_accessible_status());
        }else{
            if (
                $this->request->has('user_role_management_id') && !in_array($this->route('id'), config('commonconfig.fixed_users')) && $this->route('id') != Auth::user()->id
            ) {
                $rules['user_role_management_id'] = "required|exists:user_role_managements,id";
            }
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'user_role_management_id' => __('User Role'),
            'is_email_verified' => __('Email Status'),
            'is_active' => __('Account Status'),
            'is_financial_active' => __('Financial Status'),
            'is_accessible_under_maintenance' => __('Maintenance Access Status'),
        ];
    }
}
