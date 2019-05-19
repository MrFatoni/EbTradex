<?php

namespace App\Http\Requests\User\TradeAnalyst;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class PostRequest extends Request
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
            'title' => 'required',
            'content' => 'required',
            'featured_image' => 'image|max:512',
            'is_published' => 'required|boolean',
        ];

        if ($this->isMethod('POST')) {
            $rules['featured_image'] = 'required|image|max:512';
        }

        return $rules;
    }
}
