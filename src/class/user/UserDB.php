<?php

require_once __DIR__ . "/User.php";

class UserDB
{
    //BY:DAVI loxja 2026 ifsul
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserByID(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email FROM `user` WHERE user_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User((int) $row['user_id'], $row['user_name'], $row['user_email']);
        }
        return null;
    }

    public function getUserByEmail(int $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email FROM `user` WHERE user_email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User((int) $row['user_id'], $row['user_name'], $row['user_email']);
        }
        return null;
    }
}