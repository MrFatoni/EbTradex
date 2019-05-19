<?php

namespace App\Services\User;

use App\Http\Requests\User\UserRequest;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Interfaces\UserSettingInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function generate($parameters)
    {
        $userParams = array_only($parameters, ['email', 'username', 'is_email_verified', 'is_financial_active', 'is_accessible_under_maintenance', 'is_active']);

        $userParams['user_role_management_id'] = admin_settings('default_role_to_register');

        if (!array_has($parameters, 'password')) {
            $userParams['created_by_admin'] = random_string('6');
            $userParams['password'] = Hash::make($userParams['created_by_admin']);
        } else {
            $userParams['password'] = Hash::make($parameters['password']);
        }

        if (array_has($parameters, 'user_role_management_id')) {
            $userParams['user_role_management_id'] = $parameters['user_role_management_id'];
        }

        if (!empty($parameters['referral_code'])) {
            $referrer = app(UserInterface::class)->getFirstByConditions(['referral_code' => $parameters['referral_code'],'is_active'=> ACCOUNT_STATUS_ACTIVE]);

            if (!empty($referrer)) {
                $userParams['referrer_id'] = $referrer->id;
            }

        }

        DB::beginTransaction();
        $user = app(UserInterface::class)->create($userParams);

        if (empty($user)) {
            DB::rollBack();
            return false;
        }

        $userInfoParams = array_only($parameters, ['first_name', 'last_name', 'address', 'phone']);
        $userInfoParams['user_id'] = $user->id;
        $userInfo = app(UserInfoInterface::class)->create($userInfoParams);

        if (empty($userInfo)) {
            DB::rollBack();
            return false;
        }

        $userSettingParams = [
            'user_id' => $user->id,
            'language' => config('app.locale'),
            'timezone' => config('app.timezone')
        ];

        $userSetting = app(UserSettingInterface::class)->create($userSettingParams);

        if (empty($userSetting)) {
            DB::rollBack();
            return false;
        }

        $activeStockItems = app(StockItemInterface::class)->getActiveList()->pluck('id');
        $walletParameters = [];
        $date = date('Y-m-d h:i:s');

        foreach ($activeStockItems as $stockItemID) {
            $walletParameters[] = [
                'user_id' => $user->id,
                'stock_item_id' => $stockItemID,
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        $wallets = app(WalletInterface::class)->insert($walletParameters);

        if (empty($wallets)) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return $user;
    }
}