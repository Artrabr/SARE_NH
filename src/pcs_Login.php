<?php
session_start();

require_once __DIR__ . '/data/Connect.php';
require_once __DIR__ . '/class/user/UserDB.php';

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

function insertData($pdo){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT user_password FROM `user` WHERE user_email = :user_email");
    $stmt->bindParam(':user_email', $email);
    $stmt->execute();
    $hash = $stmt->fetchColumn();

    if(password_verify($password, $hash)){
        return true;
    }
    
    return false;
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
    $isValid = insertData($pdo);
    if($isValid){
        giveSession($pdo);
        disconnect();
        header("Location: ../public/index.php?sucesso=true");
        exit();
    }
    disconnect();
}
header("Location: ../public/index.php?sucesso=false");