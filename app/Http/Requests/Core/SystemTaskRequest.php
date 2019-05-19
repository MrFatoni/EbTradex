<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;

class SystemTaskRequest extends Request
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
        return [
            "module_id" => 'required|numeric',
            "name" => 'required|unique:system_tasks,name,'.$this->route('system_task'),
            "icon" => 'required',
            "route" => 'required',
            "order" => 'numeric',
        ];
    }
}
