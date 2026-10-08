<?php

$slotId = filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT);

if ($slotId === false || $slotId === null || $slotId < 1) {
    header("Location: ../public/index.php?sucessoSlot=false");
    exit;
}

header("Location: ../public/index.php?slot_id=" . $slotId);
exit;