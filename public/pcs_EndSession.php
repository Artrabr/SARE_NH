<?php
require_once __DIR__ . '/../src/class/User/User.php';
require_once __DIR__ . '/../src/class/Teacher/Teacher.php';
session_start();
session_destroy();
header("Location: index.php?destroySession=true");
exit();
