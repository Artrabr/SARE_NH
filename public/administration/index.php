<?php
    require_once __DIR__ . '/../../bootstrap.php';
    require_once CLASS_PATH . 'User/User.php';
    require_once CLASS_PATH . 'Teacher/Teacher.php';
    require_once CLASS_PATH . 'Auth/Auth.php';

    Auth::requireCategory('coordenador');

    $name = $_SESSION['obj_user']->getName();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header class="topbar">
        <div class="brand">SARENH</div>
        <div class="topbar-user">
            <span>Bem-vindo, <?= htmlspecialchars($name) ?>!</span>
        </div>
    </header>

    <div class="admin-shell">
        <aside class="sidebar" aria-label="Sidebar de navegação">
            <div class="sidebar-title">Menu</div>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="index.php" class="active">Painel</a></li>
                    <li><a href="timelists/index.php">Reservas</a></li>
                    <li><a href="classroomTimeList/classroomTimeList.php">Salas</a></li>
                    <li><a href="clientList/clientList.php">Usuários</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a class="sidebar-logout-link" href="../pcs_EndSession.php">Deslogar</a>
                <a class="sidebar-back-link" href="../index.php">Voltar ao mapa</a>
            </div>
        </aside>

        <main class="content">
            <section class="panel">
                <h1>Painel administrativo</h1>
                <p>Gerencie reservas, usuários e salas do sistema.</p>
            </section>

            <section class="quick-actions">
                <a href="timelists/index.php">Gerenciar horários</a>
                <a href="classroomTimeList/classroomTimeList.php">Gerenciar registros de salas</a>
                <a href="clientList/clientList.php">Gerenciar usuários e professores</a>
            </section>
        </main>
    </div>
</body>
</html>