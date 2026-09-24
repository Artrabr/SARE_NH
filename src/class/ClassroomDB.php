<?php


require_once __DIR__ . "/Classroom.php";

class ClassroomDB
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getClassroomByID(int $id): ?Classrooms
    {
        $stmt = $this->pdo->prepare("SELECT classroom_id, classroom_name, classroom_description FROM `classroom` WHERE classroom_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Classrooms((int) $row['classroom_id'], $row['classroom_name'], $row['classroom_description']);
        }
        return null;
    }

    public function getClassroomByName(string $name): ?Classrooms
    {
        $stmt = $this->pdo->prepare("SELECT classroom_id, classroom_name, classroom_description FROM `classroom` WHERE classroom_name = ?");
        $stmt->execute([$name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Classrooms((int) $row['classroom_id'], $row['classroom_name'], $row['classroom_description']);
        }
        return null;
    }
}
