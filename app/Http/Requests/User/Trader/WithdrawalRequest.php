<?php

namespace App\Http\Requests\User\Trader;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalRequest extends Request
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
        $request = [
            'amount' => 'required|numeric|between:0.00000001, 99999999999.99999999',
            'address' => 'required|max:255',
            'stock_item_type' => 'required|in:' . array_to_string(stock_item_types()),
            'accept_policy' => 'required|in:1',
        ];

        if($this->stock_item_type == CURRENCY_REAL)
        {
            $request['amount'] = 'required|numeric|between:0.01, 99999999999.99';
            $request['address'] = 'required|email';
        }

        if( env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
        {
            $request['g-recaptcha-response'] = 'required|captcha';
        }

        return $request;
    }

    public function messages()
    {
        return [
            'stock_item_type' => __('Invalid withdrawal request.'),
        ];
    }

    public function attributes()
    {
        return [
            'accept_policy' => __('The withdrawal policy checking'),
            'g-recaptcha-response' => 'google captcha'
        ];
    }
}
