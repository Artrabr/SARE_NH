<?php

require_once __DIR__ . '/../../bootstrap.php';

class Connection
{
    private static $pdo;

    public static function Connect()
    {
        if (!self::$pdo) {
            self::$pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
                DB_USERNAME,
                DB_PASSWORD
            );
        }

        return self::$pdo;
    }
}