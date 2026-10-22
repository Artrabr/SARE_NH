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

$requiredFields = ['slot_id','user_email','topic','start_time','end_time','reservation_date','reason'];

if(!checkData($_POST, $requiredFields)){
    header('Location: ' . PUBLIC_URL . '/index.php?faltadecampos=true');
    exit();
}

$pdo = connect();

$db = new ReservationDB($pdo);
$reservation = $db->createReservation(
    $_POST['user_email'],
    (int) $_POST['slot_id'],
    $_POST['topic'],
    $_POST['start_time'],
    $_POST['end_time'],
    $_POST['reservation_date'],
    $_POST['reason']
);

disconnect($pdo);
header('Location: ' . PUBLIC_URL . '/index.php?sucesso=true');
exit();
