<?php

require_once __DIR__ . '/data/Connect.php';

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
    $userlogin = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    $password = password_hash($password,PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO user (user_email, user_password, user_name) VALUES (:user_email, :user_password, :user_name)");
    $stmt->bindParam(':user_email', $userlogin);
    $stmt->bindParam(':user_password', $password);
    $stmt->bindParam(':user_name', $username);
    $stmt->execute();
}

//-------------------------
//         CÓDIGO
//-------------------------

$requiredFields = ['email', 'password','username'];

if(checkData($_POST, $requiredFields)){
    $pdo = connect();
    insertData($pdo);
    disconnect();
    header("Location: ../public/index.php");
    exit();
}
header("Location: ../public/index.php?error=1");