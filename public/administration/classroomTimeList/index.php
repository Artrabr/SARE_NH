<?php
require_once __DIR__ . '/../../../bootstrap.php';
require_once CLASS_PATH . 'Auth/Auth.php';
require_once DATA_PATH . 'Connection.php';
require_once SLOT_PATH . 'SlotDB.php';

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
                    <li><a href="index.php" class="active">Salas</a></li>
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
                <h1>Salas cadastradas</h1>
                <div class="table-wrap">
                   
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Identificação institucional</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                            <?php if ($slots === []): ?>
                                <tr>
                                    <td colspan="4">Nenhuma sala cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($slots as $slot): ?>
                                <form action="pcs_UpdateClassroomInfo.php" method="POST" class="classroom-form">
                                    
                                            <input type="hidden" name="classroom_id" value="<?= htmlspecialchars((string) $slot->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="text" name="name" value="<?= htmlspecialchars($slot->getName(), ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="text" name="institutional_name" value="<?= htmlspecialchars($slot->getInstitutionalName(), ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="text" name="description" value="<?= htmlspecialchars($slot->getDescription(), ENT_QUOTES, 'UTF-8') ?>">
                                </form>
                                <?php endforeach; ?>
                            <?php endif; ?>
                    
                </div>
                <a class="back-link" href="../index.php">Voltar para o painel administrativo</a>
            </section>
        </main>
    </div>
</body>
</html> 
<script>
    document.querySelectorAll('.classroom-form').forEach((form) => {
        form.addEventListener('change', () => form.requestSubmit());
    });
</script>