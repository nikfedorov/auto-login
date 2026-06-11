<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Auto Login Enabled
    |--------------------------------------------------------------------------
    |
    | This value determines whether the auto-login middleware is active.
    | When disabled, the middleware will always pass through without
    | attempting to auto-login any user.
    |
    */

    'enabled' => (bool) env('AUTOLOGIN_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Allowed Environments
    |--------------------------------------------------------------------------
    |
    | This is a list of application environments where the auto-login
    | middleware is allowed to operate. The value should be a comma-separated
    | list of environments (e.g., "local,staging,development").
    |
    */

    'environments' => array_filter(explode(',', (string) env('AUTOLOGIN_ENVIRONMENTS', 'local'))),

];
