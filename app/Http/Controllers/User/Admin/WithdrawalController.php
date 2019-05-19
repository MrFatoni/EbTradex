<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ReversWithdrawal;
use App\Jobs\StockItemWithdrawal;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\User\Admin\ReportsService;

class WithdrawalController extends Controller
{
    private $withdrawalRepository;

    public function __construct(WithdrawalInterface $withdrawalRepository)
    {
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function index()
    {
        $data['list'] = app(ReportsService::class)->withdrawals(null, null, 'reviewing');
        $data['title'] = __('Withdrawals for Reviewing');

        return view('backend.review_withdrawals.withdrawal', $data);
    }

    public function show($id)
    {
        $data['title'] = __('Review Withdrawal');
        $data['withdrawal'] = $this->withdrawalRepository->findOrfailById($id, ['stockItem', 'wallet', 'user', 'user.userinfo']);
        $data['user'] = $data['withdrawal']->user;

        return view('backend.review_withdrawals.show', $data);
    }

    public function approve($id)
    {
        $attributes = ['status' => PAYMENT_PENDING];
        $conditions = [ 'id' => $id, 'status' => PAYMENT_REVIEWING ];

        if( $this->withdrawalRepository->updateByConditions($attributes, $conditions) )
        {
            dispatch( new StockItemWithdrawal($id) );

            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been approved successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }

    public function decline($id)
    {
        $attributes = ['status' => PAYMENT_DECLINED];
        $conditions = [ 'id' => $id, 'status' => PAYMENT_REVIEWING ];

        if( $this->withdrawalRepository->updateByConditions($attributes, $conditions) )
        {
            dispatch(new ReversWithdrawal($id));

            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been declined successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }
}
