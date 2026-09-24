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
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_category FROM `user` WHERE user_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User((int) $row['user_id'], $row['user_name'], $row['user_email'], $row['user_category']);
        }
        return null;
    }

    /** @return User[] */
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query(
            "SELECT user_id, user_name, user_email, user_category
             FROM `user`
             ORDER BY user_name"
        );

        $users = [];
        foreach ($stmt->fetchAll() as $row) {
            $users[] = new User(
                (int) $row['user_id'],
                $row['user_name'],
                $row['user_email'],
                $row['user_category']
            );
        }

        return $users;
    }

    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_category FROM `user` WHERE user_email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User((int) $row['user_id'], $row['user_name'], $row['user_email'], $row['user_category']);
        }
        return null;
    }
}