<?php

namespace App\Providers;

use App\Models\Core\SystemNotice;
use App\Models\User\UserInfo;
use App\Observers\Core\SystemNoticeObserver;
use App\Observers\User\UserInfoObserver;
use Illuminate\Support\ServiceProvider;

class ObserverProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        UserInfo::observe(UserInfoObserver::class);
        SystemNotice::observe(SystemNoticeObserver::class);
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
