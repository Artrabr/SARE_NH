<?php

require_once __DIR__ . '/../data/Connect.php';

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
    $pdo = Connect::getInstance();
    return $pdo;
}

function insertData($pdo){
    $userlogin = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT user_password FROM user WHERE user_email = :user_email");
    $stmt->bindParam(':user_email', $userlogin);
    $stmt->execute();
    $hash = $stmt->fetchColumn();

    if(password_verify($password, $hash)){
        return true;
    }
    
    return false;
}

//-------------------------
//         CÓDIGO
//-------------------------

$requiredFields = ['login', 'password'];

if(checkData($_POST, $requiredFields)){
    $pdo = connect();
    $isValid = insertData($pdo);
    disconnect();
    if($isValid){
        header("Location: ../public/index.php");
        exit();
    }
}
header("Location: ../public/index.php");