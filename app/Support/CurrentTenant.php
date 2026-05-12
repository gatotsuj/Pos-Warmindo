<?php

namespace App\Support;

final class CurrentTenant
{
    private static ?int $id = null;

    public static function set(?int $id): void
    {
        self::$id = $id;
    }

    public static function id(): ?int
    {
        return self::$id;
    }

    public static function has(): bool
    {
        return self::$id !== null;
    }
}
