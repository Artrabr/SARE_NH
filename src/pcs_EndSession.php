<?php
require_once __DIR__ . '/class/User/User.php';
require_once __DIR__ . '/class/Teacher/Teacher.php';
session_start();
session_destroy();
header("Location: ../public/index.php?destroySession=true");
exit();
