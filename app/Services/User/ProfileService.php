<?php

namespace App\Services\User;

use App\Http\Requests\Core\PasswordUpdateRequest;
use App\Http\Requests\User\UserAvatarRequest;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\FileUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function profile()
    {
        $data['user'] = Auth::user()->load('userRoleManagement');

        return $data;
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        $update = ['password' => Hash::make($request->new_password)];

        if (app(UserInterface::class)->update($update, Auth::id())) {
            $notification = ['user_id' => Auth::id(), 'data' => __("You just changed your account's password.")];
            app(NotificationInterface::class)->create($notification);

            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('Password has been changed successfully.')
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Failed to change password.')
        ];
    }

    public function avatarUpload(UserAvatarRequest $request)
    {
        $uploadedAvatar = app(FileUploadService::class)->upload($request->file('avatar'), config('commonconfig.path_profile_image'), 'avatar', 'user', Auth::id(), 'public', 300, 300);

        if ($uploadedAvatar) {
            $parameters = ['avatar' => $uploadedAvatar];

            if (app(UserInterface::class)->update($parameters, Auth::id())) {
                return [
                    SERVICE_RESPONSE_STATUS => true,
                    SERVICE_RESPONSE_MESSAGE => __('Avatar has been uploaded successfully.')
                ];
            }
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Failed to upload the avatar.')
        ];
    }

    public function userRelatedInfo($userId)
    {
        $totalWallets = app(WalletInterface::class)->count(['user_id' => $userId]);
        $totalOpenOrders = app(StockOrderInterface::class)->count(['user_id' => $userId, 'status' => STOCK_ORDER_PENDING]);
        $totalTrades = app(StockExchangeInterface::class)->count(['user_id' => $userId]);

        return [
            'totalWallets' => $totalWallets,
            'totalOpenOrders' => $totalOpenOrders,
            'totalTrades' => $totalTrades,
        ];
    }

    public function routesForAdmin($userId)
    {
        $userRelatedInfo = $this->userRelatedInfo($userId);

        $info = [
            'walletRouteName' => 'admin.users.wallets',
            'walletRoute' => route('admin.users.wallets', ['id' => $userId]),
            'openOrderRouteName' => 'reports.admin.open-orders',
            'openOrderRoute' => route('reports.admin.open-orders', ['userId' => $userId]),
            'tradeHistoryRouteName' => 'reports.admin.trades',
            'tradeHistoryRoute' => route('reports.admin.trades', ['userId' => $userId]),
        ];

        return array_merge($userRelatedInfo, $info);
    }

    public function routesForUser($userId)
    {
        $userRelatedInfo = $this->userRelatedInfo($userId);

        $info = [
            'walletRouteName' => 'trader.wallets.index',
            'walletRoute' => route('trader.wallets.index'),
            'openOrderRouteName' => 'trader.orders.open-orders',
            'openOrderRoute' => route('trader.orders.open-orders'),
            'tradeHistoryRouteName' => 'reports.trader.trades',
            'tradeHistoryRoute' => route('reports.trader.trades'),
        ];

        return array_merge($userRelatedInfo, $info);
    }
}