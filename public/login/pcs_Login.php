<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once CLASS_PATH . 'User/User.php';
require_once CLASS_PATH . 'Teacher/Teacher.php';
require_once DATA_PATH . 'Connection.php';
require_once CLASS_PATH . 'User/UserDB.php';

function checkData($data, array $requiredFields){
    foreach($requiredFields as $field){
        if(empty($data[$field])){
            return false;
        }
    }
    return true;
}

function disconnect(){
    $pdo = null;
}

function connect(){
    $pdo = Connection::Connect();
    return $pdo;
}

function giveSession($pdo){
    $email = $_POST['email'];
    $db = new UserDB($pdo);
    $client_object = $db->getUserByEmail($email);
    $_SESSION['obj_user'] = $client_object;
}

//-------------------------
//         CÓDIGO
//-------------------------
// -------------------------------------------->>>>>>>   LEIA ISSO:  antes eu e Deus sabiamos oq tinha aqui, agora só Deus

$requiredFields = ['email', 'password'];

if(checkData($_POST, $requiredFields)){
    $pdo = connect();
    $db = new UserDB($pdo);
    if($db->checkIfPasswordMatch($_POST['email'], $_POST['password'])){
        giveSession($pdo);
        disconnect();
        header('Location: ' . PUBLIC_URL . '/index.php?sucesso=true');
        exit();
    }
    disconnect();
}
header('Location: ' . PUBLIC_URL . '/index.php?sucesso=false');