<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UserRoleManagementRequest extends Request
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
        if($this->route()->getName()=='user-role-managements.update'){
            return [
                'role_name' => [
                    'required',
                    Rule::unique('user_role_managements', 'role_name')->ignore($this->route()->parameter('id')),
                ]
            ];
        }
        else{
            return [
                'role_name' => 'required|unique:user_role_managements,role_name'
            ];
        }
    }

    public function messages()
    {
        return [
            'role_name.required' => __('The role name field is required.'),
            'role_name.unique' => __('The role name has already been taken.'),
        ];
    }

    public function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->after(function () use ($validator) {
            $routeConfigs = config('permissionRoutes.configurable_routes');
            $roles = $this->get('roles', []);

            foreach ($roles as $roleKey => $roleValue) {
                foreach ($roleValue as $roleGroupKey => $roleGroupValue) {
                    foreach ($roleGroupValue as $key => $role) {
                        if (!isset($routeConfigs[$roleKey][$roleGroupKey][$role])) {
                            unset($roles[$roleKey][$roleGroupKey][$key]);
                        }
                    }
                    if (empty($roles[$roleKey][$roleGroupKey])) {
                        unset($roles[$roleKey][$roleGroupKey]);
                    }
                }
                if (empty($roles[$roleKey])) {
                    unset($roles[$roleKey]);
                }
            }
            $this->merge(['roles' => $roles]);
            if (empty($roles)) {
                $validator->errors()->add('roles', __('The roles must have at least one access selected.'));
            }
        });
        return $validator;
    }
}
