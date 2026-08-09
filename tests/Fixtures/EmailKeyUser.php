<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

final class EmailKeyUser extends Authenticatable
{
    use HasFactory;

    public function getTable(): string
    {
        return 'users';
    }

    public function getKeyName(): string
    {
        return 'email';
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    public function getIncrementing(): bool
    {
        return false;
    }
}
