<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migrator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ডিফল্ট মাইগ্রেশন স্কিপ করার জন্য
        if ($this->app->environment('local')) {
            $this->app->bind(Migrator::class, function ($app) {
                return new class($app['migration.repository'], $app['db'], $app['files']) extends Migrator {
                    protected function runMigrationFile($file, $batch, $method)
                    {
                        // Skip default Laravel migrations
                        $skipFiles = [
                            '0001_01_01_000000_create_users_table',
                            '0001_01_01_000001_create_cache_table', 
                            '0001_01_01_000002_create_jobs_table'
                        ];
                        
                        foreach ($skipFiles as $skipFile) {
                            if (str_contains($file, $skipFile)) {
                                return;
                            }
                        }
                        
                        parent::runMigrationFile($file, $batch, $method);
                    }
                };
            });
        }
        
        Schema::defaultStringLength(191);
    }
}