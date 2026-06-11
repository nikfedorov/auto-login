# Auto Login

Middleware that automatically logs in the first database user in allowed environments — skip the login screen during development.

If no user exists yet, the request passes through unauthenticated. Run your seeders first.

## Requirements

- PHP 8.2+
- Laravel 11 / 12 / 13

## Installation

```bash
composer require nikfedorov/auto-login
```

Then register the middleware:

```bash
php artisan auto-login:install
```

This adds the middleware to `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->append(AutoLoginLocal::class);
})
```

You can also register it manually instead of running the command.

Optionally publish the config:

```bash
php artisan vendor:publish --tag=auto-login-config
```

## Configuration

| Env variable | Default | Description |
|---|---|---|
| `AUTOLOGIN_ENABLED` | `true` | Enable or disable auto-login globally |
| `AUTOLOGIN_ENVIRONMENTS` | `local` | Comma-separated list of allowed environments |

## How it works

On each request:

1. Skips if the user is already authenticated.
2. Skips if `auto-login.enabled` is `false`.
3. Skips if the current `APP_ENV` is not in `auto-login.environments`.
4. Resolves the user model from `auth.providers.users.model`, grabs the first record, and logs them in.

In any environment not listed in `AUTOLOGIN_ENVIRONMENTS` (e.g. `production`, `staging`) the middleware is a no-op.

## Testing

```bash
composer test
```
