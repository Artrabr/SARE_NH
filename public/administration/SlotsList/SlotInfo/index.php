<?php
require_once __DIR__ . '/../../../../bootstrap.php';
require_once CLASS_PATH . 'Auth/Auth.php';
require_once DATA_PATH . 'Connection.php';
require_once SLOT_PATH . 'SlotDB.php';

Auth::requireCategory('coordenador');
?>