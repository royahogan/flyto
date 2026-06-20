<?php

namespace App\Shared\Http;

class Flash
{
    private const FLASH_KEY = '_flash';
    private const OLD_INPUT_KEY = '_old_input';

    public static function success(string $message): void
    {
        self::put('success', $message);
    }

    public static function error(string $message): void
    {
        self::put('error', $message);
    }

    public static function validationErrors(array $errors): void
    {
        self::put('validationErrors', $errors);
    }

    public static function old(array $input): void
    {
        self::start();
        $_SESSION[self::OLD_INPUT_KEY] = $input;
    }

    public static function consume(): array
    {
        self::start();

        $flash = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);

        return is_array($flash) ? $flash : [];
    }

    public static function consumeOld(): array
    {
        self::start();

        $oldInput = $_SESSION[self::OLD_INPUT_KEY] ?? [];
        unset($_SESSION[self::OLD_INPUT_KEY]);

        return is_array($oldInput) ? $oldInput : [];
    }

    private static function put(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[self::FLASH_KEY][$key] = $value;
    }

    private static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
