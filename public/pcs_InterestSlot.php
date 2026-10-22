<?php

$slotId = filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT);
$slotId = $_POST['slot_id'];

if ($slotId === false || $slotId === null || $slotId < 1) {
    header("Location: index.php?sucessoSlot=false");
    exit;
}

header("Location: index.php?slot_id=" . $slotId);
exit;