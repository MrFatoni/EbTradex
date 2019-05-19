<?php

namespace App\Http\Requests\User\Admin;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StockPairRequest extends Request
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
        $stockPairRequest = [
            'stock_item_id' => 'required|exists:stock_items,id,is_active,' . ACTIVE_STATUS_ACTIVE,
            'base_item_id' => 'required|different:stock_item_id|exists:stock_items,id,is_active,' . ACTIVE_STATUS_ACTIVE,
            'last_price' => 'required|numeric|between:0.00000001, 99999999999.99999999',
        ];

        if($this->isMethod('POST')) {
            $stockPairRequest['is_active'] = 'required|in:' . array_to_string(active_status());
            $stockPairRequest['is_default'] = 'required|in:' . array_to_string(active_status());
        }

        return $stockPairRequest;
    }

    public function attributes()
    {
        return [
            'stock_item_id' => __('Active status'),
            'base_item_id' => __('Base Item'),
            'last_price' => __('Initial Price'),
            'is_active' => __('Active Status'),
        ];
    }
}
