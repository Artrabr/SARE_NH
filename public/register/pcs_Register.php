<?php

require_once __DIR__ . '/../../src/data/Connection.php';
require_once __DIR__ . '/../../src/class/User/UserDB.php';
require_once __DIR__ . '/../../src/class/Teacher/Teacher.php';
require_once __DIR__ . '/../../src/libraly/validate.php';
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

$requiredFields = ['email', 'password', 'username', 'category'];

if(!checkData($_POST, $requiredFields)){
    header("Location: ../index.php?error=1");
    exit();
}
if ($_POST['category'] === 'professor' && (!isset($_POST['subject']) || trim($_POST['subject']) === '')) {
    header("Location: ../index.php?error=1");
    exit();
}
if (!isEmailDomainAllowed($_POST['email'])) {
    header("Location: ../index.php?error=2");
    exit();
} 

$pdo = connect();
$userDB = new UserDB($pdo);

try {
    $user = $userDB->createUser($_POST['username'], $_POST['email'], $_POST['password'], $_POST['category']);
    if ($_POST['category'] === 'professor') {
        $user = new Teacher(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getCategory(),
            trim($_POST['subject'])
        );
    }
} catch (DuplicateEmail $e) {
    disconnect($pdo);
    header("Location: ../index.php?error=3");
    exit();
} catch (InvalidArgumentException $e) {
    disconnect($pdo);
    header("Location: ../index.php?error=4");
    exit();
}

$_SESSION['obj_user'] = $user;
disconnect($pdo);
header("Location: ../index.php?sucesso=true");
exit();
