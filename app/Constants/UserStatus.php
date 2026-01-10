<?php

namespace App\Constants;

class UserStatus
{
    public const INACTIVE = 0;
    public const ACTIVE = 1;

    public static function labels(): array
    {
        return [
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
        ];
    }

    public static function colors(): array
    {
        return [
            self::INACTIVE => 'danger',
            self::ACTIVE => 'success',
        ];
    }

    public static function icons(): array
    {
        return [
            self::INACTIVE => 'ki-cross',
            self::ACTIVE => 'ki-check-circle',
        ];
    }

    public static function label(int $status): string
    {
        return self::labels()[$status] ?? 'Unknown';
    }

    public static function color(int $status): string
    {
        return self::colors()[$status] ?? 'secondary';
    }

    public static function icon(int $status): string
    {
        return self::icons()[$status] ?? 'ki-question';
    }
}
