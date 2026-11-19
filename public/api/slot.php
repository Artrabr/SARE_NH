<?php

require_once __DIR__ . '/../../bootstrap.php';
require_once SLOT_PATH . 'SlotDB.php';
require_once DATA_PATH . 'Connection.php';

header('Content-Type: application/json; charset=utf-8');

function respondWithJson(array $payload, int $statusCode): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    respondWithJson(['error' => 'Método não permitido.'], 405);
}

$slotId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$institutionalName = filter_input(INPUT_GET, 'institutional_name', FILTER_UNSAFE_RAW);

if ($slotId !== null) {
    if ($slotId === false || $slotId < 1) {
        respondWithJson(['error' => 'Identificador de sala inválido.'], 400);
    }
} elseif (is_string($institutionalName) && trim($institutionalName) !== '') {
    $institutionalName = trim($institutionalName);
} else {
    respondWithJson(['error' => 'Informe o identificador ou o nome institucional da sala.'], 400);
}

try {
    $slotDb = new SlotDB(Connection::Connect());
    $slot = $slotId !== null
        ? $slotDb->getSlotByID($slotId)
        : $slotDb->getSlotByInstitutionalName($institutionalName);
} catch (PDOException $error) {
    error_log($error->getMessage());
    respondWithJson(['error' => 'Não foi possível consultar os dados da sala.'], 500);
}

if ($slot === null) {
    respondWithJson(['error' => 'Sala não encontrada.'], 404);
}

respondWithJson([
    'id' => $slot->getId(),
    'name' => $slot->getName(),
    'institutionalName' => $slot->getInstitutionalName(),
    'description' => $slot->getDescription(),
], 200);
