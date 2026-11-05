<?php

require_once __DIR__ . '/../../../bootstrap.php';
require_once DATA_PATH . 'Connection.php';
require_once CLASS_PATH . 'Reservation/ReservationDB.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método não permitido.');
}

$reservationId = filter_var($_POST['reservation_id'] ?? null, FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? null;

if (
    $reservationId === false ||
    $reservationId === null ||
    $reservationId < 1 ||
    !is_string($status)
) {
    header('Location: index.php?status=error');
    exit;
}

try{
    $pdo = Connection::Connect();
    $reservationDB = new ReservationDB($pdo);

    switch ($status){ //TEM CERTEZA DE QUE É ESSES 4 VALORES APENAS?
        case 'pending': // sim sim, pode confiar pq vem pelo select
            $reservationDB->updateReservationStatus($reservationId, $status);
            break;      // coloco default ou noa?
        case 'confirmed':// sla, poe se quiser nap vamo usar msm
            $reservationDB->updateReservationStatus($reservationId, $status);
            break;      // mas se der merda?
        case 'rejected':// por mim whatever  
            $reservationDB->updateReservationStatus($reservationId, $status);
            break;      // vou por default ent
        case 'cancelled':
            $reservationDB->updateReservationStatus($reservationId, $status);
            break;
        default:
            throw new Exception('Status inválido');
        break;
    }
} catch (Exception $e) {
    // Redireciona para a página de reservas com uma mensagem de erro
    header('Location: index.php?status=error');
    exit;
}

header('Location: index.php?status=success');
exit;
?>