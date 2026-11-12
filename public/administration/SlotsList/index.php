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
    <link rel="icon" type="image/x-icon" href="../../midia/img/icone-reservas-ifsul-nh.svg"><!--LOGO DO SITE-->
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
                    <table class="classroom-table">
                        <thead>
                            <tr>
                                <th scope="col">Identificação institucional</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($slots === []): ?>
                                <tr>
                                    <td colspan="3" class="empty-state">Nenhuma sala cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($slots as $slot): ?>
                                    <?php $formId = $slot->getId(); ?>
                                    <tr>
                                        <td>
                                            <a href="./SlotInfo/index.php?id=<?=$formId?>>" 
                                            class="classroom-input" 
                                            name="institutional_name" 
                                            aria-label="Identificação institucional"
                                            >
                                                <?= htmlspecialchars($slot->getInstitutionalName(), ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </td>
                                        <td>
                                            <form id="<?= htmlspecialchars($formId, ENT_QUOTES, 'UTF-8') ?>" action="pcs_UpdateClassroomInfo.php" method="POST" class="classroom-form">
                                                <input type="hidden" name="classroom_id" value="<?= htmlspecialchars((string) $slot->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="institutional_name" value="<?= htmlspecialchars($slot->getInstitutionalName(), ENT_QUOTES, 'UTF-8') ?>">
                                            </form>
                                            <input
                                                form="<?= htmlspecialchars($formId, ENT_QUOTES, 'UTF-8') ?>"
                                                class="classroom-input"
                                                type="text"
                                                name="name"
                                                aria-label="Nome da sala"
                                                value="<?= htmlspecialchars($slot->getName(), ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                        </td>
                                        <td>
                                            <input
                                                form="<?= htmlspecialchars($formId, ENT_QUOTES, 'UTF-8') ?>"
                                                class="classroom-input"
                                                type="text"
                                                name="description"
                                                aria-label="Descrição da sala"
                                                value="<?= htmlspecialchars($slot->getDescription(), ENT_QUOTES, 'UTF-8') ?>"
                                            >
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
    document.querySelectorAll('.classroom-input').forEach((input) => {
        input.addEventListener('change', () => input.form.requestSubmit());
    });
</script>
</body>
</html>