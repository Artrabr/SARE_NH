<?php

require_once __DIR__ . '/../../bootstrap.php';
require_once DATA_PATH . 'Connection.php';
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

$requiredFields = ['email', 'password', 'username', 'category'];

if(!checkData($_POST, $requiredFields)){
    header('Location: ' . PUBLIC_URL . '/index.php?error=1');
    exit();
}
$email = strtolower(trim($_POST['email']));
$username = strtolower(trim($_POST['username']));

if ($_POST['category'] === 'professor' && (!isset($_POST['subject']) || trim($_POST['subject']) === '')) {
    header('Location: ' . PUBLIC_URL . '/index.php?error=noSubject');
    exit();
}
if (!isEmailDomainAllowed($email)) {
    header('Location: ' . PUBLIC_URL . '/index.php?error=2');
    exit();
} 

$pdo = connect();
$userDB = new UserDB($pdo);

try {
    if ($userDB->getUserByEmail($email)) {
        disconnect($pdo);
        header('Location: ' . PUBLIC_URL . '/index.php?error=alreadyCreated');
        exit();
    }
} catch (PDOException $e) {
    disconnect($pdo);
    error_log('ERRO ao consultar usuário por e-mail: ' . $e->getMessage());
    header('Location: ' . PUBLIC_URL . '/index.php?error=5');
    exit();
}

try {
    $user = $userDB->createUser($username, $email, $_POST['password'], $_POST['category']);
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
    header('Location: ' . PUBLIC_URL . '/index.php?error=3');
    exit();
} catch (InvalidArgumentException $e) {
    disconnect($pdo);
    header('Location: ' . PUBLIC_URL . '/index.php?error=4');
    exit();
}

$_SESSION['obj_user'] = $user;
disconnect($pdo);
header('Location: ' . PUBLIC_URL . '/index.php?sucesso=true');
exit();
