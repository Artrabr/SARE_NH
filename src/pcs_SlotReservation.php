<?php

require_once __DIR__ . '/data/Connection.php';
require_once __DIR__ . '/class/Reservation/ReservationDB.php';
require_once __DIR__ . '/class/User/UserDB.php';
require_once __DIR__ . '/class/Teacher/Teacher.php';
require_once __DIR__ . '/libraly/validate.php';
session_start();

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

$requiredFields = ['slot_id','user_email','topic','start_time','end_time','reason'];

if(!checkData($_POST, $requiredFields)){
    header("Location: ../public/index.php?faltadecampos=true");
    exit();
}

$pdo = connect();

$db = new ReservationDB($pdo);
$reservation = $db->createReservation($_POST['user_email'], (int) $_POST['slot_id'], $_POST['topic'], $_POST['start_time'], $_POST['end_time'], $_POST['reason']);

disconnect($pdo);
header("Location: ../public/index.php?sucesso=true");
exit();
