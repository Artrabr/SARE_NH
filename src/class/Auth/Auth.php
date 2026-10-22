<?php

require_once __DIR__ . '/../../../bootstrap.php';

class Auth
{
    public static function requireCategory(string $category, string $redirect = 'PUBLIC_URL'): void
    {
        if (
            !isset($_SESSION['obj_user']) ||
            $_SESSION['obj_user']->getCategory() !== $category
        ) {
            header('Location: ' . $redirect . 'index.php');
            exit();
        }
    }
}