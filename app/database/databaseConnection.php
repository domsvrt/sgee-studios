<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class DatabaseConnection
{
    private static ?PDO $connection = null;

    public static function get(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        self::loadEnv();

        $name = getenv('DB_NAME') ?: 'sgee_studios';
        $user = getenv('DB_USER') ?: 'sgee_user';
        $password = getenv('DB_PASSWORD') ?: 'sgee_password';
        $host = getenv('DB_HOST') ?: 'db';

        self::$connection = new PDO(
            "mysql:host={$host};dbname={$name};charset=utf8mb4",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$connection;
    }

    private static function loadEnv(): void
    {
        $path = __DIR__ . '/../../.env';

        if (!is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));

            if (getenv($key) === false) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }
}
