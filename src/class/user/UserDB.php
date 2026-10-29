<?php

require_once __DIR__ . "/User.php";
require_once __DIR__ . "/DuplicateEmail.php";

class UserDB
{
    //BY:DAVI 2026 ifsul
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setAdmin(string $input): bool
    {
        $input = strtolower($input);
        return $input === 'coordenador' || $input === 'diretor';
    }

    public function getUserByID(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_category FROM `user` WHERE user_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $admin = $this->setAdmin($row['user_category']);

        return new User((int) $row['user_id'], $row['user_name'], $row['user_email'], $row['user_category'], $admin);
    }
    /* @return User[] */
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query(
            "SELECT user_id, user_name, user_email, user_category
             FROM `user`
             ORDER BY user_name"
        );

        $users = [];
        foreach ($stmt->fetchAll() as $row) {
            $admin = $this->setAdmin($row['user_category']);
            $users[] = new User(
                (int) $row['user_id'],
                $row['user_name'],
                $row['user_email'],
                $row['user_category'],
                $admin
            );
        }

        return $users;
    }

    public function getUserByEmail(string $email): ?User
    //retorna ou user ou null
    {
        $stmt = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_category FROM `user` WHERE user_email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $admin = $this->setAdmin($row['user_category']);

        return new User((int) $row['user_id'], $row['user_name'], $row['user_email'], $row['user_category'],$admin);
    }

    public function checkIfPasswordMatch(string $email, string $password): bool
    {
        $stmt = $this->pdo->prepare("SELECT user_password FROM `user` WHERE user_email = ?");
        $stmt->execute([$email]);
        $hash = $stmt->fetchColumn();

        return is_string($hash) && password_verify($password, $hash);
    }

    public function createUser(string $name, string $email, string $password, string $category): ?User
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $this->pdo->prepare("INSERT INTO `user` (user_name, user_email, user_password, user_category) VALUES (:name, :email, :password, :category)");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'category' => $category,
            ]);
            $id = (int) $this->pdo->lastInsertId();
            $admin = $this->setAdmin($category);

            return new User($id, $name, $email, $category, $admin);
        } catch (PDOException $e) {
            //error for when the email already exists
            if ($e->getCode() == '23000' && ($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateEmail("Email already used", 409);
            }
            throw $e;
        }
    }
}
