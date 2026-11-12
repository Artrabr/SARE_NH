<?php
    require_once __DIR__ . '/../../../bootstrap.php';
    require_once CLASS_PATH . 'Auth/Auth.php';

    Auth::requireCategory('coordenador');

    require_once CLASS_PATH . 'Reservation/ReservationDB.php';
    require_once SLOT_PATH . 'SlotDB.php';
    require_once CLASS_PATH . 'User/UserDB.php';
    require_once DATA_PATH . 'Connection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require __DIR__ . '/pcs_UpdateReservationStatus.php';
    }

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
    <link rel="icon" type="image/x-icon" href="../../midia/img/icone-reservas-ifsul-nh.svg"><!--LOGO DO SITE-->
    <title>Pedidos de reserva</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <header class="topbar">
        <div class="brand">SARENH</div>
        <div class="topbar-user">
            <span>Administração</span>
        </div>
    </header>

    <div class="admin-shell">
        <aside class="sidebar" aria-label="Sidebar de navegação">
            <div class="sidebar-title">Menu</div>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="../index.php">Painel</a></li>
                    <li><a href="index.php" class="active">Reservas</a></li>
                    <li><a href="../SlotsList/index.php">Salas</a></li>
                    <li><a href="../clientList/index.php">Usuários</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a class="sidebar-logout-link" href="../../pcs_EndSession.php">Deslogar</a>
                <a class="sidebar-back-link" href="../../index.php">Voltar ao mapa</a>
            </div>
        </aside>

        <main class="content">
            <section class="panel table-panel">
                <h1>Reservas de salas</h1>
                <div> <!-- ===========================Mensagens de status=========================== -->
                <?php if (($_GET['status'] ?? '') === 'success'): ?>
                    <p role="status">Status da reserva atualizado.</p>
                <?php elseif (($_GET['status'] ?? '') === 'error'): ?>
                    <p role="alert">Não foi possível atualizar o status da reserva.</p>
                <?php elseif (($_GET['status'] ?? '') === 'noChange'): ?>
                    <p role="status">O status selecionado já estava aplicado.</p>
                <?php endif; ?>
                </div>
                <div class="table-wrap">
                    <table>
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
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($reservations === []): ?>
                                <tr>
                                    <td colspan="9">Nenhuma reserva encontrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservations as $reservation):
                                    $user = $userDB->getUserByID($reservation->getUserId());
                                    $status = $reservation->getStatus();
                                    $reservationId = $reservation->getId();
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
                                        <td>
                                            <form action="index.php" method="POST">
                                                <input type="hidden" name="reservation_id" value="<?= $reservationId ?>">
                                                <select
                                                    name="status"
                                                    id="status-<?= $reservationId ?>"
                                                    class="reservation-status"
                                                    aria-label="Status da reserva #<?= $reservationId ?>"
                                                >
                                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                                    <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Aprovada</option>
                                                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejeitada</option>
                                                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelada</option>
                                                </select>
                                                <noscript><button type="submit">Atualizar</button></noscript>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <a class="back-link" href="../index.php">Voltar para o painel administrativo</a>
            </section>
        </main>
    </div>
    <script>
        document.querySelectorAll('.reservation-status').forEach((select) => {
            select.addEventListener('change', () => select.form.requestSubmit());
        });
    </script>
</body>
</html>