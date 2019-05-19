<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\UserRoleManagementRequest;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Services\Core\DataListService;


class UserRoleManagementsController extends Controller
{
    public $roleManagement;

    public function __construct(UserRoleManagementInterface $roleManagement)
    {
        $this->roleManagement = $roleManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchFields = [
            ['role_name', __('Role Name')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['role_name', __('Role Name')],
        ];

        $query = $this->roleManagement->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Role Management');
        $data['defaultRoles'] = config('commonconfig.fixed_roles');
        if (!is_array($data['defaultRoles'])) {
            $data['defaultRoles'] = [];
        }

        return view('backend.userRoleManagements.index', $data);
    }

    public function create()
    {
        $data['routes'] = config('permissionRoutes.configurable_routes');
        $data['title'] = __('Create User Role');

        return view('backend.userRoleManagements.create', $data);
    }

    public function store(UserRoleManagementRequest $request)
    {
        $parameters = [
            'role_name' => $request->role_name,
            'route_group' => $request->roles
        ];

        if ($userRoleManagement = $this->roleManagement->create($parameters)) {
            cache()->forever("userRoleManagement" .$userRoleManagement->id , $userRoleManagement->route_group);
            return redirect()->route('user-role-managements.edit', $userRoleManagement->id)->with(SERVICE_RESPONSE_SUCCESS, __('User role has been created successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user role.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['routes'] = config('permissionRoutes.configurable_routes');
        $data['userRoleManagement'] = $this->roleManagement->findOrFailById($id);
        $data['title'] = __('Edit User Role');

        return view('backend.userRoleManagements.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRoleManagementRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRoleManagementRequest $request, $id)
    {
        $roles = $request->roles;

        if ($id == USER_ROLE_SUPER_ADMIN) {
            $roles[ROUTE_SECTION_USER_MANAGEMENTS][ROUTE_SUB_SECTION_ROLE_MANAGEMENTS] = [
                ROUTE_GROUP_READER_ACCESS,
                ROUTE_GROUP_CREATION_ACCESS,
                ROUTE_GROUP_MODIFIER_ACCESS,
                ROUTE_GROUP_DELETION_ACCESS
            ];
        }

        $parameters = [
            'role_name' => $request->role_name,
            'route_group' => $roles
        ];

        if ($userRoleManagement = $this->roleManagement->update($parameters, $id)) {
            cache()->forever("userRoleManagement" .$id , $parameters['route_group']);
            return redirect()->route('user-role-managements.edit', $id)->with(SERVICE_RESPONSE_SUCCESS, __('User role has been updated successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user role.'));
    }

    public function destroy($id)
    {
        if ($this->roleManagement->deleteById($id)) {
            return redirect()->route('user-role-managements.index')->with(SERVICE_RESPONSE_SUCCESS, __('User role has been deleted successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('This role cannot be deleted.'));
    }

    public function changeStatus($id)
    {
        if($updatedState = $this->roleManagement->toggleStatusById($id)){
            return redirect()->route('user-role-managements.index')->with(SERVICE_RESPONSE_SUCCESS,__('User role has been :state successfully',['state'=> $updatedState]));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('User role status can not be changed'));
    }
}
