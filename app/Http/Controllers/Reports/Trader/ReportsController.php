<?php

namespace App\Http\Controllers\Reports\Trader;

use App\Http\Controllers\Controller;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Trader\Interfaces\DepositInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\User\Admin\ReportsService;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    private $reportsService;
    private $depositRepository;
    private $withdrawalRepository;

    public function __construct(DepositInterface $deposit, WithdrawalInterface $withdrawal, ReportsService $reportsService)
    {
        $this->depositRepository = $deposit;
        $this->withdrawalRepository = $withdrawal;
        $this->reportsService = $reportsService;
    }

    public function allDeposits($paymentTransactionType = null)
    {
        $data['list'] = $this->reportsService->deposits(Auth::id(), null, $paymentTransactionType);
        $data['title'] = __('Deposits');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.all_deposit', $data);
    }

    public function deposits($id, $paymentTransactionType = null)
    {
        $data['wallet'] = app(WalletInterface::class)->firstOrFail(['id' => $id, 'user_id' => Auth::id()], 'stockItem');
        $data['list'] = $this->reportsService->deposits(Auth::id(), $id, $paymentTransactionType);
        $data['title'] = __('Deposits');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.deposit', $data);
    }

    public function allWithdrawals($paymentTransactionType = null)
    {
        $data['list'] = $this->reportsService->withdrawals(Auth::id(), null, $paymentTransactionType);
        $data['title'] = __('Withdrawals');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.all_withdrawal', $data);
    }

    public function withdrawals($id, $paymentTransactionType = null)
    {
        $data['wallet'] = app(WalletInterface::class)->firstOrFail(['id' => $id, 'user_id' => Auth::id()], 'stockItem');
        $data['list'] = $this->reportsService->withdrawals(Auth::id(), $id, $paymentTransactionType);
        $data['title'] = __('Withdrawals');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.withdrawal', $data);
    }

    public function trades($categoryType = null)
    {
        $data['list'] = $this->reportsService->trades(Auth::id(), $categoryType);
        $data['title'] = __('Trades');
        $data['categoryType'] = $categoryType;

        return view('frontend.reports.trades', $data);
    }

    public function referralUsers()
    {
        $data['list'] = $this->reportsService->referralUsers(Auth::id());
        $data['title'] = __('Trades');

        return view('frontend.reports.referral_users', $data);
    }

    public function referralEarning()
    {
        try {
            $userId = decrypt(request()->get('ref'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Referral earning not found for this request.'));
        }

        $data['list'] = $this->reportsService->referralEarning(\auth()->id(), $userId);
        $data['referralUserInfo'] = app(UserInfoInterface::class)->findOrFailByConditions(['user_id' => $userId]);
        $data['title'] = __('Referral Earning');
//        dd($data);
        return view('frontend.reports.referral_earning', $data);
    }
}