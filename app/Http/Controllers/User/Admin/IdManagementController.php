<?php

namespace App\Http\Controllers\User\Admin;

use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Http\Controllers\Controller;
use App\Services\Core\DataListService;

class IdManagementController extends Controller
{
    public $userInfo;

    /**
     * IdManagementController constructor.
     * @param UserInfoInterface $user
     */
    public function __construct(UserInfoInterface $user)
    {
        $this->userInfo = $user;
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 4:01 PM
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $searchFields = [
            ['email', __('Email')],
        ];

        $orderFields = [
            ['email', __('Email')],
        ];

        $joinArray = ['users', 'users.id', '=', 'user_infos.user_id'];

        $select = ['users.id as id', 'email', 'id_type', 'is_id_verified'];
        $query = $this->userInfo->paginateWithFilters($searchFields, $orderFields, ['is_id_verified', '!=', ID_STATUS_UNVERIFIED], $select, $joinArray);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('ID Management');

        return view('backend.idManagement.index', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 5:36 PM
     * @description:
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $where = ['user_id'=> $id, ['is_id_verified', '!=', ID_STATUS_UNVERIFIED]];
        $data['user'] = $this->userInfo->findOrFailByConditions($where, ['user']);
        $data['title'] = __('View ID Verification Request');

        return view('backend.idManagement.show', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 5:36 PM
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id) {
        try {
            $conditions = ['user_id'=> $id, 'is_id_verified' => ID_STATUS_PENDING];
            $attributes = ['is_id_verified' => ID_STATUS_VERIFIED];

            if (!$this->userInfo->updateByConditions($attributes, $conditions)) {
                throw new \Exception('Failed to approve.');
            }

            $notification = ['user_id' => $id, 'data' => __("Your ID verification request has been approved.")];
            app(NotificationInterface::class)->create($notification);

            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The ID has been approved successfully.'));
        }
        catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to approve.'));
        }
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 5:37 PM
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decline($id)
    {
        try {
            $attributes = [
                'is_id_verified' => ID_STATUS_UNVERIFIED,
                'id_type' => null,
                'id_card_front' => null,
                'id_card_back' => null,
            ];

            $conditions = ['user_id'=> $id, 'is_id_verified' => ID_STATUS_PENDING];

            if (!$this->userInfo->updateByConditions($attributes, $conditions)) {
                throw new \Exception('Failed to decline.');
            }

            $notification = ['user_id' => $id, 'data' => __("Your ID verification request has been declined.")];
            app(NotificationInterface::class)->create($notification);

            return redirect()->route('admin.id-management.index')->with(SERVICE_RESPONSE_SUCCESS, __('The ID has been declined successfully.'));
        }
        catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to decline.'));
        }
    }
}