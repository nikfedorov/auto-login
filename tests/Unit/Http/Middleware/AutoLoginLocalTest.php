<?php

declare(strict_types=1);

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Nikfedorov\AutoLogin\Http\Middleware\AutoLoginLocal;
use Tests\Fixtures\User;

beforeEach(function (): void {
    Config::set('app.env', 'local');
    Config::set('auto-login.enabled', true);
    Config::set('auto-login.environments', ['local']);

    $this->middleware = new AutoLoginLocal;
});

it('automatically logs in first user', function (): void {
    // arrange
    $user = User::factory()->create(['name' => 'First User']);

    // act
    $this->middleware->handle(request(), fn ($req): ResponseFactory|Response => response('OK'));

    // assert
    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->id)->toBe($user->id);
});

it('does not override existing auth', function (): void {
    // arrange
    User::factory()->create(['name' => 'First User']);
    $user = User::factory()->create(['name' => 'Second User']);
    Auth::login($user);

    // act
    $this->middleware->handle(request(), fn ($req): ResponseFactory|Response => response('OK'));

    // assert
    expect(Auth::user()->id)->toBe($user->id);
});

it('does not login when auto-login is disabled', function (): void {
    // arrange
    Config::set('auto-login.enabled', false);
    User::factory()->create(['name' => 'First User']);

    // act
    $this->middleware->handle(request(), fn ($req): ResponseFactory|Response => response('OK'));

    // assert
    expect(Auth::check())->toBeFalse();
});

it('does not login when environment is not allowed', function (): void {
    // arrange
    Config::set('app.env', 'production');
    Config::set('auto-login.environments', ['staging']);
    User::factory()->create(['name' => 'First User']);

    // act
    $this->middleware->handle(request(), fn ($req): ResponseFactory|Response => response('OK'));

    // assert
    expect(Auth::check())->toBeFalse();
});
