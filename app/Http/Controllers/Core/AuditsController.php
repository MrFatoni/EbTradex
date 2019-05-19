<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Repositories\Core\Interfaces\AuditInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\DB;

class AuditsController extends Controller
{
    protected $audit;

    public function __construct(AuditInterface $audit)
    {
        $this->audit = $audit;
    }

    public function index()
    {
        $searchFields = [
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['event', __('Event')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['email', __('Email')],
            ['created_ar', __('Date')],
        ];

        $select = ['audits.*', 'email', DB::raw("CONCAT(first_name,' ',last_name) as full_name")];
        $join = [
            ['users', 'users.id', '=', 'audits.user_id'],
            ['user_infos', 'user_infos.user_id', '=', 'users.id'],
        ];

        $query = $this->audit->paginateWithFilters($searchFields, $orderFields, null, $select, $join);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Audits');

        return view('backend.audits.index', $data);
    }
}
