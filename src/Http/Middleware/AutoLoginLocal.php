<?php

declare(strict_types=1);

namespace Nikfedorov\AutoLogin\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginLocal
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        if (! Config::boolean('auto-login.enabled', true)) {
            return $next($request);
        }

        $environments = Config::array('auto-login.environments', ['local']);
        $currentEnv = Config::string('app.env');

        if (in_array($currentEnv, $environments, true)) {
            /** @var class-string<Model&Authenticatable> $userModel */
            $userModel = Config::string('auth.providers.users.model');

            /** @var (Model&Authenticatable)|null $user */
            $user = $userModel::query()->first();

            if ($user instanceof Authenticatable) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
