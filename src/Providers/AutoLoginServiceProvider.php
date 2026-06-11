<?php

declare(strict_types=1);

namespace Nikfedorov\AutoLogin\Providers;

use Illuminate\Support\ServiceProvider;
use Nikfedorov\AutoLogin\Console\Commands\AutoLoginInstallCommand;

class AutoLoginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/auto-login.php', 'auto-login');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([AutoLoginInstallCommand::class]);

            $this->publishes([
                __DIR__.'/../../config/auto-login.php' => config_path('auto-login.php'),
            ], 'auto-login-config');
        }
    }
}
