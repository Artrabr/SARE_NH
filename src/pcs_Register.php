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

    $password = password_hash($password);

    $stmt = $pdo->prepare("INSERT INTO user (user_email, user_password) VALUES (:user_email, :user_password)");
    $stmt->bindParam(':user_email', $userlogin);
    $stmt->bindParam(':user_password', $password);
    $stmt->execute();
}

//-------------------------
//         CÓDIGO
//-------------------------

$requiredFields = ['login', 'password'];

if(checkData($_POST, $requiredFields)){
    $pdo = connect();
    insertData($pdo);
    disconnect();
    header("Location: ../public/index.php");
    exit();
}
header("Location: ../public/index.php");