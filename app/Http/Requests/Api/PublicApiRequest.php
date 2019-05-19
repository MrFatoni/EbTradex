<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class PublicApiRequest extends Request
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
            'command' => "required|in:" . array_to_string(allowed_public_api_command(),',', false),
            'coinPair' => "required_if:command,returnChartData",
            'interval' => "required_if:command,returnChartData|in:" . array_to_string(chart_data_interval()),
        ];

        return $rules;
    }
}
