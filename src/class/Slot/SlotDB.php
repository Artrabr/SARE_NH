<?php

require_once __DIR__ . "/Slot.php";

class SlotDB
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSlotByID(int $id): ?Slot
    {
        $stmt = $this->pdo->prepare("SELECT slot_id, slot_name, slot_institutional_name, slot_description FROM `slot` WHERE slot_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Slot(
                (int) $row['slot_id'],
                $row['slot_name'],
                $row['slot_institutional_name'],
                $row['slot_description']
            );
        }
        return null;
    }

    public function getSlotByInstitutionalName(string $institutionalName): ?Slot
    {
        $stmt = $this->pdo->prepare("SELECT slot_id, slot_name, slot_institutional_name, slot_description FROM `slot` WHERE slot_institutional_name = ?");
        $stmt->execute([$institutionalName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Slot(
                (int) $row['slot_id'],
                $row['slot_name'],
                $row['slot_institutional_name'],
                $row['slot_description']
            );
        }
        return null;
    }
    
    public function getAllSlots(): array
    {
        $stmt = $this->pdo->query(
            "SELECT slot_id, slot_name, slot_institutional_name, slot_description
             FROM `slot`
             ORDER BY slot_name"
        );

        $slots = [];
        foreach ($stmt->fetchAll() as $row) {
            $slots[] = new Slot(
                (int) $row['slot_id'],
                $row['slot_name'],
                $row['slot_institutional_name'],
                $row['slot_description']
            );
        }

        return $slots;
    }

    public function createSlot(string $name, string $institutionalName, string $description): ?Slot
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO `user` (user_name, user_email, user_password, user_category) VALUES (:name, :email, :password, :category)");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'category' => $category,
            ]);
            $id = (int) $this->pdo->lastInsertId();

            return new User($id, $name, $email, $category);
        } catch (PDOException $e) {
            //error for when the email already exists
            if ($e->getCode() == '23000' && ($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateEmail("Email already used", 409);
            }
            throw $e;
        }
    }
}
