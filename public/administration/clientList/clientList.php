<?php
require_once __DIR__ . '/../../../bootstrap.php';
require_once CLASS_PATH . 'Auth/Auth.php';
require_once DATA_PATH . 'Connection.php';
require_once CLASS_PATH . 'User/UserDB.php';

Auth::requireCategory('coordenador');

$users = (new UserDB(Connection::Connect()))->getAllUsers();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de registrados</title>
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
                    <li><a href="../classroomTimeList/classroomTimeList.php">Salas</a></li>
                    <li><a href="clientList.php" class="active">Usuários</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a class="sidebar-logout-link" href="../../pcs_EndSession.php">Deslogar</a>
                <a class="sidebar-back-link" href="../../index.php">Voltar ao mapa</a>
            </div>
        </aside>

        <main class="content">
            <section class="panel table-panel">
                <h1>Usuários registrados</h1>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user->getCategory(), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a class="back-link" href="../index.php">Voltar para o painel administrativo</a>
            </section>
        </main>
    </div>
</body>
</html>