<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Services\Core\AdminSettingService;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public $adminSettingService;

    public function __construct(AdminSettingService $adminSettingService)
    {
        $this->adminSettingService = $adminSettingService;
    }

    public function index($adminSettingType = 'general')
    {
        $data['settings'] = $this->adminSettingService->adminForm($adminSettingType, true);
        $data['adminSettingType'] = $adminSettingType;
        $data['title'] = __('Admin Setting');

        return view('backend.adminSetting.index', $data);
    }

    public function edit($adminSettingType)
    {
        $data['settings'] = $this->adminSettingService->adminForm($adminSettingType);
        $data['adminSettingType'] = $adminSettingType;
        $data['title'] = __('Edit Admin Setting');

        return view('backend.adminSetting.edit', $data);
    }

    public function update(Request $request, $adminSettingType)
    {
        $response = $this->adminSettingService->adminUpdate($request, $adminSettingType);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('admin-settings.edit',$adminSettingType)->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}