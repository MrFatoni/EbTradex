<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public $notification;

    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
    }

    public function index()
    {
        $user = Auth::user();
        $data['title'] = __('Notices');

        $searchFields = [
            ['data', __('Notice')],
        ];

        $orderFields = [
            ['id', __('Serial')],
            ['data', __('Notice')],
            ['created_at', __('Date')],
            ['read_at', __('Status')],
        ];

        $where = ['user_id' => $user->id];
        $query = $this->notification->paginateWithFilters($searchFields, $orderFields, $where);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);

        return view('backend.notices.index', $data);
    }

    public function markAsRead($id)
    {
        if($this->notification->read($id)){
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS,__('The notice has been marked as read.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR,__('Failed to mark as read.'));
    }

    public function markAsUnread($id)
    {
        if($this->notification->unread($id)){
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS,__('The notice has been marked as unread.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR,__('Failed to mark as unread.'));
    }
}
