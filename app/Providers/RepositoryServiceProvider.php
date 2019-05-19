<?php

namespace App\Providers;

use App\Repositories\Core\Eloquent\AdminSettingRepository;
use App\Repositories\Core\Eloquent\AuditRepository;
use App\Repositories\Core\Eloquent\NavigationRepository;
use App\Repositories\Core\Eloquent\SystemNoticeRepository;
use App\Repositories\Core\Eloquent\UserRoleManagementRepository;
use App\Repositories\Core\Interfaces\AdminSettingInterface;
use App\Repositories\Core\Interfaces\AuditInterface;
use App\Repositories\Core\Interfaces\NavigationInterface;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Repositories\Exchange\Eloquent\StockExchangeGroupRepository;
use App\Repositories\Exchange\Eloquent\StockExchangeRepository;
use App\Repositories\Exchange\Eloquent\StockGraphDataRepository;
use App\Repositories\Exchange\Interfaces\StockExchangeGroupInterface;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\Exchange\Interfaces\StockGraphDataInterface;
use App\Repositories\User\Admin\Eloquent\StockItemRepository;
use App\Repositories\User\Admin\Eloquent\StockPairRepository;
use App\Repositories\User\Admin\Eloquent\TransactionRepository;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Eloquent\CommentRepository;
use App\Repositories\User\Eloquent\NotificationRepository;
use App\Repositories\User\Eloquent\UserInfoRepository;
use App\Repositories\User\Eloquent\UserRepository;
use App\Repositories\User\Eloquent\UserSettingRepository;
use App\Repositories\User\Interfaces\CommentInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Interfaces\UserSettingInterface;
use App\Repositories\User\TradeAnalyst\Eloquent\PostRepository;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use App\Repositories\User\Trader\Eloquent\DepositRepository;
use App\Repositories\User\Trader\Eloquent\QuestionRepository;
use App\Repositories\User\Trader\Eloquent\ReferralEarningRepository;
use App\Repositories\User\Trader\Eloquent\StockOrderRepository;
use App\Repositories\User\Trader\Eloquent\WalletRepository;
use App\Repositories\User\Trader\Eloquent\WithdrawalRepository;
use App\Repositories\User\Trader\Interfaces\DepositInterface;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Repositories\User\Trader\Interfaces\ReferralEarningInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(AdminSettingInterface::class, AdminSettingRepository::class);
        $this->app->bind(UserRoleManagementInterface::class, UserRoleManagementRepository::class);
        $this->app->bind(NavigationInterface::class, NavigationRepository::class);
        $this->app->bind(SystemNoticeInterface::class, SystemNoticeRepository::class);
        $this->app->bind(NotificationInterface::class, NotificationRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(UserInfoInterface::class, UserInfoRepository::class);
        $this->app->bind(UserSettingInterface::class, UserSettingRepository::class);
        $this->app->bind(AuditInterface::class, AuditRepository::class);
        $this->app->bind(StockItemInterface::class, StockItemRepository::class);
        $this->app->bind(WalletInterface::class, WalletRepository::class);
        $this->app->bind(StockPairInterface::class, StockPairRepository::class);
        $this->app->bind(StockOrderInterface::class, StockOrderRepository::class);
        $this->app->bind(StockExchangeInterface::class, StockExchangeRepository::class);
        $this->app->bind(TransactionInterface::class, TransactionRepository::class);
        $this->app->bind(StockExchangeGroupInterface::class, StockExchangeGroupRepository::class);
        $this->app->bind(StockGraphDataInterface::class, StockGraphDataRepository::class);
        $this->app->bind(DepositInterface::class, DepositRepository::class);
        $this->app->bind(WithdrawalInterface::class, WithdrawalRepository::class);
        $this->app->bind(PostInterface::class, PostRepository::class);
        $this->app->bind(QuestionInterface::class, QuestionRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
        $this->app->bind(ReferralEarningInterface::class, ReferralEarningRepository::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
