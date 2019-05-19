<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UserStatusRequest;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\Core\DataListService;
use App\Services\User\UserService;
use App\Services\User\Trader\WalletService;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $searchFields = [
            ['username', __('Username')],
            ['email', __('Email')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];
        $orderFields = [
            ['first_name', __('First Name')],
            ['users.id', __('Serial')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['username', __('Username')],
            ['users.created_at', __('Registered Date')],
        ];
        $joinArray = [
            ['user_role_managements', 'user_role_managements.id', '=', 'users.user_role_management_id'],
            ['user_infos', 'user_infos.user_id', '=', 'users.id'],
        ];
        $select = [
            'users.*', 'role_name', 'first_name', 'last_name'
        ];

        $query = $this->user->paginateWithFilters($searchFields, $orderFields, null, $select, $joinArray);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Users');

        return view('backend.users.index', $data);
    }

    public function show($id)
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('View User');

        return view('backend.users.show', $data);
    }

    public function create()
    {
        $data['userRoleManagements'] = app(UserRoleManagementInterface::class)->getUserRoles();
        $data['title'] = __('Create User');

        return view('backend.users.create', $data);
    }

    public function store(UserRequest $request)
    {
        $parameters = $request->only(['first_name','last_name','address','user_role_management_id','email','username','is_email_verified','is_financial_active','is_active','is_accessible_under_maintenance']);

        if ($user = app(UserService::class)->generate($parameters)) {
            return redirect()->route('users.show', $user->id)->with(SERVICE_RESPONSE_SUCCESS, __("User has been created successfully."));
        } else {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user.'));
        }
    }

    public function edit($id, UserRoleManagementInterface $userRoleManagement)
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['userRoleManagements'] = $userRoleManagement->getUserRoles();
        $data['title'] = __('Edit User');

        return view('backend.users.edit', $data);
    }

    public function update(UserRequest $request, $id)
    {
        if (!in_array($id, config('commonconfig.fixed_users')) && $id != Auth::id()) {
            $parameters['user_role_management_id'] = $request->user_role_management_id;
            $notification = ['user_id' => $id, 'data' => __("Your account's role has been changed by admin.")];
            $this->user->update($parameters, $id);
        }

        $parameters = $request->only('first_name', 'last_name', 'address');

        if (app(UserInfoInterface::class)->update($parameters, $id, 'user_id')) {
            if (isset($notification)) {
                app(NotificationInterface::class)->create($notification);
            }

            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('User has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user'));
    }

    public function editStatus($id)
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('Edit User Status');

        return view('backend.users.edit_status', $data);
    }

    public function updateStatus(UserStatusRequest $request, $id)
    {
        if ($id == Auth::id()) {
            return redirect()->route('users.edit.status', $id)->with(SERVICE_RESPONSE_WARNING, __('You cannot change your own status.'));
        } elseif (in_array($id, config('commonconfig.fixed_users'))) {
            return redirect()->route('users.edit.status', $id)->with(SERVICE_RESPONSE_WARNING, __("You cannot change primary user's status."));
        }

        $messages = [
            'is_email_verified' => __('Your email verification status has been changed by admin.'),
            'is_financial_active' => __("Your account's financial status has been changed by admin."),
            'is_accessible_under_maintenance' => __("Your account's maintenance mode access has been changed by admin."),
            'is_active' => __("Your account's status has been changed by admin."),
        ];

        $fields = array_keys($messages);
        $parameters = $request->only($fields);
        $user = $this->user->getFirstById($id);

        if (!$this->user->update($parameters, $id)) {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user status.'));
        }

        $date = date('Y-m-d H:s:i');
        $notifications = [];

        foreach ($fields as $field) {
            if ($user->{$field} != $parameters[$field]) {
                $notifications[] = ['user_id' => $id, 'data' => $messages[$field], 'created_at' => $date, 'updated_at' => $date];
            }
        }

        if (!empty($notifications)) {
            app(NotificationInterface::class)->insert($notifications);
        }

        return redirect()->route('users.edit.status', $id)->with(SERVICE_RESPONSE_SUCCESS, __('User status has been updated successfully.'));
    }

    public function wallets($id) {
        $data['list'] = app(WalletService::class)->getWallets($id);
        $data['title'] = __('Wallets');

        return view('backend.users.wallets.index', $data);
    }
}