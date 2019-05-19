<?php

namespace App\Http\Controllers\User\Admin;


use App\Http\Controllers\Controller;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\User\Admin\DashboardService;

class DashboardController extends Controller
{
    private $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function __invoke()
    {
        $data['title'] = __('Dashboard');

        $data['cpuUsages'] = $this->dashboardService->getCpuUsages();
        $data['stockPairs'] = app(StockPairInterface::class)->getAllStockPairDetailByConditions(['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE]);
        $data['totalStockItem'] = app(StockItemInterface::class)->getCountByConditions(['is_active'=>ACTIVE_STATUS_ACTIVE]);
        $data['totalUser'] = app(UserInterface::class)->getCountByConditions(['is_active'=>ACTIVE_STATUS_ACTIVE]);

        return view('backend.dashboard.superadmin', $data);
    }
}
