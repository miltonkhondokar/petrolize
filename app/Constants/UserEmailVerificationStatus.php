<?php

namespace App\Constants;

class UserEmailVerificationStatus
{
    public const UNVERIFIED = 0;
    public const VERIFIED = 1;

    public static function labels(): array
    {
        return [
            self::UNVERIFIED => 'Unverified',
            self::VERIFIED => 'Verified',
        ];
    }

    public static function colors(): array
    {
        return [
            self::UNVERIFIED => 'warning',
            self::VERIFIED => 'success',
        ];
    }

    public static function icons(): array
    {
        return [
            self::UNVERIFIED => 'ki-shield-cross',
            self::VERIFIED => 'ki-check',
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
