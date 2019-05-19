<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class Google2faRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            'google_app_code' => 'required|numeric',
        ];

        if($this->isMethod('PUT')) {
            $rules['password'] = 'required|hash_check:' . Auth::user()->password;
            $rules['back_up'] = 'required|in:1';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'password.hash_check' => __('Current password is wrong.')
        ];
    }

    public function attributes()
    {
        return [
            'google_app_code' => __('G2FA'),
            'back_up' => __('Checking'),
        ];
    }
}