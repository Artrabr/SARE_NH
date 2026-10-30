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
            <section class="panel">
                <h1>Registro de salas</h1>
                <p>Visualize e acompanhe os relatórios da estrutura de salas do campus.</p>
                <div class="report-image-wrap">
                    <img src="relatorio.png" alt="Relatório de salas" class="report-image">
                </div>
                <a class="back-link" href="../index.php">Voltar para o painel administrativo</a>
            </section>
        </main>
    </div>
</body>
</html>