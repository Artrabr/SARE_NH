<?php

class TeacherDB {
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTeacherByID(int $id): ?Teacher
    {
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_category, user_subject FROM `user` WHERE user_id = ? AND user_category = 'professor'");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Teacher((int) $row['user_id'], $row['user_name'], $row['user_email'], $row['user_category'], (string) $row['user_subject']);
        }
        return null;
    }

    public function getTeacherByEmail(string $email): ?Teacher
    {
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_category, user_subject FROM `user` WHERE user_email = ? AND user_category = 'professor'");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Teacher((int) $row['user_id'], $row['user_name'], $row['user_email'], $row['user_category'], (string) $row['user_subject']);
        }
        return null;
    }
}