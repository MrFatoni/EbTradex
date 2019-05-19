<?php

namespace App\Http\Controllers\Core;

use App\Services\Core\NavService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NavController extends Controller
{
    public function index($slug=null){
        $data = app(NavService::class)->backendMenuBuilder($slug);
        $data['title'] = __('Navigation');

        return view('backend.navigation.index',$data);
    }

    public function save(Request $request, $slug){
        $response = app(NavService::class)->backendMenuSave($request, $slug);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}