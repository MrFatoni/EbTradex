<?php

namespace App\Providers;

use App\Repositories\Core\Interfaces\AdminSettingInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class PermissionProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadAdminSettings();
    }

    private function loadAdminSettings()
    {
        $adminSettings = admin_settings();
        if (empty($adminSettings)) {
            try {
                $adminSettings = $this->app->make(AdminSettingInterface::class)->getAll();
                $adminSettings = $adminSettings->pluck('value', 'slug')->toArray();
                foreach ($adminSettings as $key => $val) {
                    if (is_json($val)) {
                        $adminSettings[$key] = json_decode($val, true);
                    }
                }
            } catch (\Exception $e) {
                $adminSettings = ['lang' => LANGUAGE_DEFAULT];
            }
            Cache::forever('admin_settings', $adminSettings);
        }
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
