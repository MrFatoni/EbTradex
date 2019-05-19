<?php

namespace App\Http\Requests\User\Trader;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class IcoStoreRequest extends FormRequest
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
        $minIcoAmountBuy = admin_settings('min_ico_amount_buy');

        return [
            'stock_pair_id' => 'required|integer',
            'amount' => 'required|numeric|between:'. $minIcoAmountBuy .',99999999999.99999999',
        ];
    }

    public function messages()
    {
        $errorMessage = __('Invalid Request.');

        return [
            'stock_pair_id.required' => $errorMessage,
            'stock_pair_id.integer' => $errorMessage
        ];
    }
}
