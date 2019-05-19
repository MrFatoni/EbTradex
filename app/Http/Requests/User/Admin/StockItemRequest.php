<?php

namespace App\Http\Requests\User\Admin;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StockItemRequest extends Request
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
        $stockApiServices = api_services();

        if( $this->get('item_type') == CURRENCY_CRYPTO )
        {
            $stockApiServices = crypto_currency_api_services();
        }
        elseif ($this->get('item_type') == CURRENCY_REAL)
        {
            $stockApiServices = real_currency_api_services();
        }

        $request = [
            'item' => [
                'required',
                'alpha',
                Rule::unique('stock_items')->ignore($this->route()->parameter('id')),
                'max:255'
            ],
            'item_name' => [
                'required',
                Rule::unique('stock_items')->ignore($this->route()->parameter('id')),
                'max:255'
            ],
            'item_type' => 'required|in:' . array_to_string(stock_item_types()),
            'item_emoji' => 'image|max:2048',
            'is_active' => 'required|in:' . array_to_string(active_status()),
            'is_ico' => 'required|in:' . array_to_string(active_status()),
            'exchange_status' => 'required_if:is_ico,'. ACTIVE_STATUS_INACTIVE .'|in:' . array_to_string(active_status()),
        ];

        if( $this->get('is_ico') == ACTIVE_STATUS_INACTIVE )
        {
            $request['deposit_status'] = 'required_if:item_type,' . CURRENCY_REAL .',item_type,' . CURRENCY_CRYPTO . '|in:' . array_to_string(active_status());
            $request['deposit_fee'] = 'required_if:deposit_status,' . ACTIVE_STATUS_ACTIVE . '|numeric|between:0, 99999999999.99';
            $request['withdrawal_status'] = 'required_if:item_type,' . CURRENCY_REAL .',item_type,' . CURRENCY_CRYPTO . '|in:' . array_to_string(active_status());
            $request['minimum_withdrawal_amount'] = 'required_unless:item_type,' . CURRENCY_VIRTUAL . '|numeric|between:0,9999999999999.99999999';
            $request['daily_withdrawal_limit'] = 'required_if:withdrawal_status,' . ACTIVE_STATUS_ACTIVE . '|numeric|between:0,9999999999999.99999999';
            $request['withdrawal_fee'] = 'required_if:withdrawal_status,' . ACTIVE_STATUS_ACTIVE . '|numeric|between:0, 99999999999.99';
        }

        if(
            $this->get('deposit_status') == ACTIVE_STATUS_ACTIVE ||
            $this->get('withdrawal_status') == ACTIVE_STATUS_ACTIVE
        )
        {
            $request['api_service'] = 'required_if:is_ico,' . ACTIVE_STATUS_INACTIVE . '|in:' . array_to_string($stockApiServices);
        }

        return $request;
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-15 4:57 PM
     * @description: Custom messages
     * @return array
     */
    public function messages()
    {
        return [
            'deposit_status.required_if' => __('The deposit status field is required when item type is real currency / crypto currency.'),
            'withdrawal_status.required_if' => __('The withdrawal status field is required when item type is real currency / crypto currency.'),
            'deposit_fee.required_if' => __('The deposit fee field is required when deposit status is active.'),
            'withdrawal_fee.required_if' => __('The withdrawal fee field is required when withdrawal status is active.'),
            'api_service.required_if' => __('The api service field is required when deposit status / withdrawal status is active.'),
            'api_service.in' => __('The api service is invalid or not available for this stock item.'),
            'minimum_withdrawal_amount.required_if' => __('The minimum withdrawal amount field is required when withdrawal is active and the currency is real or crypto.'),
            'daily_withdrawal_limit.required_if' => __('The daily withdrawal limit field is required when withdrawal is active and the currency is real or crypto.'),
        ];
    }

    public function attributes()
    {
        return [
            'is_active' => __('Active status'),
        ];
    }
}