<?php

namespace App\Constants;

class UserType
{
    public const ADMIN = 'Admin';
    public const EXECUTIVE = 'Executive';
    public const FUEL_STATION_MANAGER = 'Fuel Station Manager';

    /**
     * All roles
     */
    public static function all(): array
    {
        return [
            self::ADMIN,
            self::EXECUTIVE,
            self::FUEL_STATION_MANAGER,
        ];
    }

    /**
     * Role labels for UI
     */
    public static function labels(): array
    {
        return [
            self::ADMIN => 'Admin',
            self::EXECUTIVE => 'Executive',
            self::FUEL_STATION_MANAGER => 'Fuel Station Manager',
        ];
    }

    /**
     * Get label
     */
    public static function label(string $role): string
    {
        return self::labels()[$role] ?? 'Unknown';
    }

    /**
     * Bootstrap / Tailwind badge classes
     */
    public static function badgeClass(string $role): string
    {
        return match ($role) {
            self::ADMIN => 'badge-light-danger',       // red
            self::EXECUTIVE => 'badge-light-primary',  // blue
            self::FUEL_STATION_MANAGER => 'badge-light-success', // green
            default => 'badge-light-secondary',
        };
    }
}
