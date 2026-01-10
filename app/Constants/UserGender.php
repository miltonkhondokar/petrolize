<?php

namespace App\Constants;

class UserGender
{
    public const MALE = 1;
    public const FEMALE = 2;

    public static function labels(): array
    {
        return [
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        ];
    }

    public static function colors(): array
    {
        return [
            self::MALE => 'primary',
            self::FEMALE => 'info',
        ];
    }

    public static function icons(): array
    {
        return [
            self::MALE => 'ki-male',
            self::FEMALE => 'ki-female',
        ];
    }

    public static function label(?int $gender): string
    {
        return self::labels()[$gender] ?? 'Unknown';
    }

    public static function color(?int $gender): string
    {
        return self::colors()[$gender] ?? 'secondary';
    }

    public static function icon(?int $gender): string
    {
        return self::icons()[$gender] ?? 'ki-user';
    }
}
