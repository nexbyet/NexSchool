<?php

namespace App\Providers;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Only share school settings when actually installed
        View::composer('*', function ($view) {
            if (file_exists(base_path('.env')) && file_exists(storage_path('installed.lock'))) {
                try {
                    $schoolSetting = SchoolSetting::find(1);
                    $view->with('schoolSetting', $schoolSetting);
                    return;
                } catch (\Exception $e) {
                    // DB not ready yet
                }
            }
            $view->with('schoolSetting', null);
        });
    }
}
