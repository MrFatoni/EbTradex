<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class PasswordUpdateRequest extends Request
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
        return [
            'password' => 'required|hash_check:' . Auth::user()->password,
            'new_password' => 'required|confirmed|between:6,32',
        ];
    }

    public function messages()
    {
        return [
            'password.hash_check' => __('Current password is wrong.')
        ];
    }
}
