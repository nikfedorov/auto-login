<?php

declare(strict_types=1);

namespace Nikfedorov\AutoLogin\Console\Commands;

use Illuminate\Console\Command;
use Nikfedorov\AutoLogin\Http\Middleware\AutoLoginLocal;

class AutoLoginInstallCommand extends Command
{
    protected $signature = 'auto-login:install';

    protected $description = 'Add the AutoLoginLocal middleware to bootstrap/app.php';

    public function handle(): int
    {
        $bootstrapPath = base_path('bootstrap/app.php');

        if (! is_readable($bootstrapPath)) {
            $this->error('Could not read bootstrap/app.php');

            return self::FAILURE;
        }

        $middlewareClass = AutoLoginLocal::class;
        $useStatement = sprintf('use %s;', $middlewareClass);
        $appendStatement = sprintf('$middleware->append(%s::class);', class_basename($middlewareClass));

        $contents = (string) file_get_contents($bootstrapPath);

        if (str_contains($contents, $appendStatement)) {
            $this->info('AutoLoginLocal middleware is already registered.');

            return self::SUCCESS;
        }

        // Add use statement after the last existing use statement
        if (! str_contains($contents, $useStatement)) {
            $contents = preg_replace(
                '/(^use [^\n]+;\n)(?!use )/m',
                sprintf('$1%s%s', $useStatement, PHP_EOL),
                $contents,
                1,
            ) ?? $contents;
        }

        // Add middleware inside withMiddleware callback
        $contents = preg_replace(
            '/->withMiddleware\(function \(Middleware \$middleware\): void \{/',
            '->withMiddleware(function (Middleware $middleware): void {
        '.$appendStatement,
            $contents,
            1,
        ) ?? $contents;

        file_put_contents($bootstrapPath, $contents);

        $this->info('AutoLoginLocal middleware added to bootstrap/app.php');

        return self::SUCCESS;
    }
}
