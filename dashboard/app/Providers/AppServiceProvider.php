<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Auto-ensure SQLite database file exists if SQLite connection is used
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && $dbPath !== ':memory:') {
                $directory = dirname($dbPath);
                if (!is_dir($directory)) {
                    @mkdir($directory, 0755, true);
                }
                if (!file_exists($dbPath)) {
                    @touch($dbPath);
                }
            }
        }

        // Auto-migrate and seed if tables are missing
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Auto-migration error: ' . $e->getMessage());
        }

        Blade::component('layouts.guest', 'guest-layout');
        Blade::component('layouts.app', 'app-layout');
    }
}
