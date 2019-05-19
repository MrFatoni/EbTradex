<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UserAvatarRequest extends Request
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
            'avatar' => 'required|image|max:2048'
        ];
    }
}