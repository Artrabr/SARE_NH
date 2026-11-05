<?php

require_once __DIR__ . '/../bootstrap.php';
require_once DATA_PATH . 'Connection.php';
require_once CLASS_PATH . 'Reservation/ReservationDB.php';
require_once CLASS_PATH . 'User/UserDB.php';
require_once CLASS_PATH . 'Teacher/Teacher.php';
require_once LIBRARY_PATH . 'validate.php';

function checkData($data, array $requiredFields){
    foreach($requiredFields as $field){
        if(empty($data[$field])){
            return false;
        }
    }
    return true;
}

function disconnect(&$pdo){
    $pdo = null;
}

function connect(){
    $pdo = Connection::Connect();
    return $pdo;
}

//-------------------------
//         CÓDIGO
//-------------------------

if (!isset($_SESSION['obj_user']) || !method_exists($_SESSION['obj_user'], 'getId')) {
    header('Location: ' . PUBLIC_URL . '/login/login.php');
    exit();
}

$requiredFields = ['slot_id', 'topic', 'start_time', 'end_time', 'reason'];
$reservationDate = $_POST['reservation_date'] ?? date('Y-m-d');

if (
    !checkData($_POST, $requiredFields) ||
    !is_string($reservationDate) ||
    !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $reservationDate, $dateParts) ||
    !checkdate((int) $dateParts[2], (int) $dateParts[3], (int) $dateParts[1])
) {
    header('Location: ' . PUBLIC_URL . '/index.php?faltadecampos=true');
    exit();
}

$slotId = filter_var($_POST['slot_id'], FILTER_VALIDATE_INT);
if ($slotId === false || $slotId < 1) {
    header('Location: ' . PUBLIC_URL . '/index.php?faltadecampos=true');
    exit();
}

$startTime = $_POST['start_time'];
$endTime = $_POST['end_time'];
$validTimeFormat = is_string($startTime)
    && is_string($endTime)
    && preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $startTime)
    && preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $endTime);

if (!$validTimeFormat || $endTime <= $startTime) {
    $redirect = PUBLIC_URL . '/index.php?horario_invalido=true';
    $redirect .= '&slot_id=' . $slotId;
    header('Location: ' . $redirect);
    exit();
}

$pdo = connect();

$db = new ReservationDB($pdo);
try {
    $reservation = $db->createReservation(
        (int) $_SESSION['obj_user']->getId(),
        $slotId,
        $_POST['topic'],
        $startTime,
        $endTime,
        $reservationDate,
        $_POST['reason']
    );
} catch (DomainException $e) {
    disconnect($pdo);
    header('Location: ' . PUBLIC_URL . '/index.php?horario_ocupado=true&slot_id=' . $slotId);
    exit();
}

disconnect($pdo);
header('Location: ' . PUBLIC_URL . '/index.php?sucesso=true');
exit();
