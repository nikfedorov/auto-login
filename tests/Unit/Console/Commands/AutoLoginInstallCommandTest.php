<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Nikfedorov\AutoLogin\Http\Middleware\AutoLoginLocal;

beforeEach(function (): void {
    // Create a temp dir to simulate a Laravel project root — no real project dependency
    $this->tempDir = sys_get_temp_dir().'/auto-login-test-'.uniqid();
    mkdir($this->tempDir.'/bootstrap', 0777, true);

    file_put_contents($this->tempDir.'/bootstrap/app.php', <<<'PHP'
<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware): void {
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
PHP);

    $this->basePathRef = new ReflectionProperty($this->app, 'basePath');
    $this->originalBasePath = $this->basePathRef->getValue($this->app);
    $this->basePathRef->setValue($this->app, $this->tempDir);
});

afterEach(function (): void {
    $this->basePathRef->setValue($this->app, $this->originalBasePath);
    @unlink($this->tempDir.'/bootstrap/app.php');
    @rmdir($this->tempDir.'/bootstrap');
    @rmdir($this->tempDir);
});

it('adds middleware to bootstrap/app.php', function (): void {
    // act
    Artisan::call('auto-login:install');

    // assert
    $updated = file_get_contents($this->tempDir.'/bootstrap/app.php');
    expect($updated)->toContain(sprintf('use %s;', AutoLoginLocal::class));
    expect($updated)->toContain('$middleware->append(AutoLoginLocal::class);');
});

it('skips when middleware is already registered', function (): void {
    // arrange — write a stub bootstrap that already has the middleware
    file_put_contents($this->tempDir.'/bootstrap/app.php', <<<'PHP'
<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Nikfedorov\AutoLogin\Http\Middleware\AutoLoginLocal;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(AutoLoginLocal::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
PHP
    );

    // act
    $exitCode = Artisan::call('auto-login:install');

    // assert
    expect($exitCode)->toBe(0);
    expect(Artisan::output())->toContain('already registered');
});

it('returns failure when bootstrap/app.php cannot be read', function (): void {
    // arrange — remove the bootstrap file
    unlink($this->tempDir.'/bootstrap/app.php');

    // act
    $exitCode = Artisan::call('auto-login:install');

    // assert
    expect($exitCode)->toBe(1);
    expect(Artisan::output())->toContain('Could not read');
});
