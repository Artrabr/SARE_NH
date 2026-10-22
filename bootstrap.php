<?php

define('RAIZ', __DIR__ . DIRECTORY_SEPARATOR);
define('PROJECT_ROOT', __DIR__);

$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$projectRoot = realpath(PROJECT_ROOT);
$projectUrl = '';

if ($documentRoot !== false && $projectRoot !== false) {
    $documentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');
    $projectRoot = str_replace('\\', '/', $projectRoot);

    if (strcasecmp($projectRoot, $documentRoot) !== 0) {
        $documentRootPrefix = $documentRoot . '/';
        if (stripos($projectRoot, $documentRootPrefix) === 0) {
            $projectUrl = substr($projectRoot, strlen($documentRoot));
        }
    }
}

define('PROJECT_URL', rtrim($projectUrl, '/'));
define('PUBLIC_URL', PROJECT_URL . '/public');
define('SRC_PATH', PROJECT_ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
define('CLASS_PATH', SRC_PATH . 'class' . DIRECTORY_SEPARATOR);
define('DATA_PATH', SRC_PATH . 'data' . DIRECTORY_SEPARATOR);
define('LIBRARY_PATH', SRC_PATH . 'libraly' . DIRECTORY_SEPARATOR);

require_once CLASS_PATH . 'User/User.php';
require_once CLASS_PATH . 'Teacher/Teacher.php';

if (file_exists(PROJECT_ROOT . DIRECTORY_SEPARATOR . 'db_constants.php')) {
    require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'db_constants.php';
}

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', 3306);
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'sarenh');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}

date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
