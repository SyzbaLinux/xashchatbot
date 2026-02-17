<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    public function redirectPath(): string
    {
        return match ($this) {
            self::Admin => '/admin/dashboard',
            self::User => '/user/dashboard',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::User => 'User',
        };
    }
}
