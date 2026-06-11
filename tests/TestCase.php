<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Nikfedorov\AutoLogin\Providers\AutoLoginServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Fixtures\User;

abstract class TestCase extends BaseTestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function defineEnvironment($app): void
    {
        $app->make(Repository::class)->set('auth.providers.users.model', User::class);
        $app->make(Repository::class)->set('auth.providers.users.driver', 'eloquent');
        $app->make(Repository::class)->set('auth.guards.web.provider', 'users');
    }

    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [AutoLoginServiceProvider::class];
    }
}
