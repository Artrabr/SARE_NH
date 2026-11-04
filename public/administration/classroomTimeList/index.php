<?php
require_once __DIR__ . '/../../../bootstrap.php';
require_once CLASS_PATH . 'Auth/Auth.php';
require_once DATA_PATH . 'Connection.php';
require_once CLASS_PATH . 'Slot/SlotDB.php';

Auth::requireCategory('coordenador');

$slots = (new SlotDB(Connection::Connect()))->getAllSlots();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de salas</title>
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
                    <li><a href="../timelists/index.php">Reservas</a></li>
                    <li><a href="classroomTimeList.php" class="active">Salas</a></li>
                    <li><a href="../clientList/clientList.php">Usuários</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a class="sidebar-logout-link" href="../../pcs_EndSession.php">Deslogar</a>
                <a class="sidebar-back-link" href="../../index.php">Voltar ao mapa</a>
            </div>
        </aside>

        <main class="content">
            <section class="panel table-panel">
                <h1>Salas cadastradas</h1>
                <h2 style="color: red;">arrumar essa pagina. os campos tem q ser alteraveis pelo formulario e adicionar campo de interditado</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Identificação institucional</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($slots === []): ?>
                                <tr>
                                    <td colspan="4">Nenhuma sala cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($slots as $slot): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) $slot->getId(), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($slot->getName(), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($slot->getInstitutionalName(), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($slot->getDescription(), ENT_QUOTES, 'UTF-8') ?></td>
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
</body>
</html>