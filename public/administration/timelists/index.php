<?php
    require_once __DIR__ . '/../../../bootstrap.php';
    require_once CLASS_PATH . 'Auth/Auth.php';

    Auth::requireCategory('coordenador');

    require_once CLASS_PATH . 'Reservation/ReservationDB.php';
    require_once CLASS_PATH . 'Slot/SlotDB.php';
    require_once CLASS_PATH . 'User/UserDB.php';
    require_once DATA_PATH . 'Connection.php';

    $pdo = Connection::Connect();
    $reservationDB = new ReservationDB($pdo);
    $reservations = $reservationDB->getAllReservation();
    $userDB = new UserDB($pdo);

    $slotDB = new SlotDB($pdo);
    $slotsById = [];
    foreach ($slotDB->getAllSlots() as $slot) {
        $slotsById[$slot->getId()] = $slot->getName();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos de reserva</title>
</head>
<body>
    <main>
        <section>
            <div>
                <table>
                    <caption>Reservas de salas</caption>
                    <thead>
                        <tr>
                            <th scope="col">Usuário</th>
                            <th scope="col">Sala</th>
                            <th scope="col">Tópico</th>
                            <th scope="col">Início</th>
                            <th scope="col">Fim</th>
                            <th scope="col">Data</th>
                            <th scope="col">Justificativa</th>
                            <th scope="col">Solicitado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reservations === []): ?>
                            <tr>
                                <td colspan="8">Nenhuma reserva encontrada.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $reservation):
                                $userID = $reservation->getUserId();
                                $user = $userDB->getUserByID($userID);
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($user ? $user->getName() : 'Usuário não encontrado', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($slotsById[$reservation->getSlot()] ?? 'Sala não encontrada', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($reservation->getTopic(), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(substr($reservation->getStartTime(), 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(substr($reservation->getEndTime(), 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($reservation->getReservationDate(), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($reservation->getReason() ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($reservation->getReservedAt(), ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <a href="../index.php">Voltar para o painel administrativo</a>
    </main>
</body>
</html>