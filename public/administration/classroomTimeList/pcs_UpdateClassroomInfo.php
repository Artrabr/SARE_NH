<?php

require_once __DIR__ . '/../../../bootstrap.php';
require_once DATA_PATH . 'Connection.php';
require_once CLASS_PATH . 'Classroom/ClassroomDB.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    exit('Método não permitido.');
}

$name = $_POST['name'] ?? null;
$institutionalName = $_POST['institutional_name'] ?? null;
$classroomId = filter_var($_POST['classroom_id'] ?? null, FILTER_VALIDATE_INT);

if ($classroomId === false || $classroomId === null || $classroomId < 1) {
    header('Location: index.php?status=error');
    exit;
}

try {
    $pdo = Connection::Connect();
    $classroomDB = new ClassroomDB($pdo);

    $classroomDB->updateClassroomInfo($classroomId, $name, $institutionalName);
} catch (Exception $e) {
    // Redireciona para a página de salas com uma mensagem de erro
    header('Location: index.php?status=error');
    exit;
}