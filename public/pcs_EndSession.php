<?php
require_once __DIR__ . '/../bootstrap.php';
require_once CLASS_PATH . 'User/User.php';
require_once CLASS_PATH . 'Teacher/Teacher.php';
session_destroy();
header('Location: ' . PUBLIC_URL . '/index.php?destroySession=true');
exit();
