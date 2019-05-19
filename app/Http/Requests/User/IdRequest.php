<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class IdRequest extends Request
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
            'id_type' => 'required|in:' . array_to_string(id_type()),
            'id_card_front' => 'required|image|max:2048',
            'id_card_back' => 'required_unless:id_type,' . ID_PASSPORT . '|image|max:2048',
        ];
    }
}
