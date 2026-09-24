<?php

require_once __DIR__ . '/../../src/data/Connect.php';

function checkData($data, array $requiredFields){
    foreach($requiredFields as $field){
        if(empty($data[$field])){
            return false;
        }
    }
    return true;
}

function isEmailDomainAllowed(string $email): bool {$domainWhiteList = 'ifsul.edu.br';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $parts = explode('@', $email);
    $domain = end($parts);
    return in_aray($domain, $domainWhiteList);
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
    $category = $_POST['category'];

    $allowedCategories = ['professor', 'coordenador', 'servidor', 'diretor'];
    if (!in_array($category, $allowedCategories, true)) {
        return false;
    }

    $password = password_hash($password,PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO `user` (user_email, user_password, user_name, user_category) VALUES (:user_email, :user_password, :user_name, :category)");
    $stmt->bindParam(':user_email', $userlogin);
    $stmt->bindParam(':user_password', $password);
    $stmt->bindParam(':user_name', $username);
    $stmt->bindParam(':category', $category);
    $stmt->execute();

    return true;
}

//-------------------------
//         CÓDIGO
//-------------------------

$requiredFields = ['email', 'password', 'username', 'category'];

if(checkData($_POST, $requiredFields)){
    $pdo = connect();
    $isRegistered = insertData($pdo);
    disconnect();
    if ($isRegistered) {
        header("Location: ../index.php");
        exit();
    }
}
header("Location: ../index.php?error=1");