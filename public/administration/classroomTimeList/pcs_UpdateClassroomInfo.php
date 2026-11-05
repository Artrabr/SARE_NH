<?php

require_once __DIR__ . '/../../../bootstrap.php';
require_once DATA_PATH . 'Connection.php';
require_once SLOT_PATH . 'SlotDB.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    exit('Método não permitido.');
}

$name = $_POST['name'] ?? null;
$institutionalName = $_POST['institutional_name'] ?? null;
$description = $_POST['description'] ?? null;
$classroomId = filter_var($_POST['classroom_id'] ?? null, FILTER_VALIDATE_INT);

if (
    $classroomId === false ||
    $classroomId === null ||
    $classroomId < 1 ||
    !is_string($name) ||
    !is_string($institutionalName) ||
    !is_string($description)
) {
    header('Location: index.php?status=error');
    exit;
}

try {
    $pdo = Connection::Connect();
    $slotDB = new SlotDB($pdo);
    $slotDB->updateSlotInfo($classroomId, $name, $institutionalName, $description);
} catch (Exception $e) {
    header('Location: index.php?status=error');
    exit;
}

header('Location: index.php?status=success');
exit;