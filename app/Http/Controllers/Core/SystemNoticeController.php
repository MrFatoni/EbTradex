<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\SystemNoticeRequest;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use App\Services\Core\DataListService;

class SystemNoticeController extends Controller
{
    public $systemNotice;

    public function __construct(SystemNoticeInterface $systemNotice)
    {
        $this->systemNotice = $systemNotice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchFields = [
            ['title', __('Title')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['type', __('Type')],
            ['status', __('Status')],
            ['start_at', __('Start Time')],
            ['end_at', __('End Time')],
        ];

        $query = $this->systemNotice->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('System Notice');

        return view('backend.systemNotice.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['types'] = array_combine(config('commonconfig.system_notice_types'), array_map('ucfirst', config('commonconfig.system_notice_types')));
        $data['title'] = __('Create Notice');

        return view('backend.systemNotice.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SystemNoticeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SystemNoticeRequest $request)
    {
        $noticeInput = $request->only(['title', 'description', 'start_at', 'end_at', 'status', 'type']);
        $notice = $this->systemNotice->create($noticeInput);

        if (!empty($notice)) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('Notice has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create notice.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data['systemNotice'] = $this->systemNotice->findOrFailById($id);
        $data['types'] = array_combine(config('commonconfig.system_notice_types'), array_map('ucfirst', config('commonconfig.system_notice_types')));
        $data['title'] = __('Edit Notices');

        return view('backend.systemNotice.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SystemNoticeRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SystemNoticeRequest $request, $id)
    {
        $systemNotice = $request->only(['title', 'description', 'start_at', 'end_at', 'status', 'type']);

        if ($this->systemNotice->update($systemNotice, $id)) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('System notice has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update system notice.'));
    }

    public function destroy($id)
    {
        if ( $this->systemNotice->deleteById($id) ) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('System notice has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete system notice.'));
    }
}