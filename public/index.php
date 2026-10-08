<?php
require_once __DIR__ . '/../src/class/user/User.php';
require_once __DIR__ . '/../src/class/Teacher/Teacher.php';
require_once __DIR__ . '/../src/class/Slot/SlotDB.php';
require_once __DIR__ . '/../src/data/Connection.php';
session_start();
$usuarioLogado = isset($_SESSION['obj_user']);
$slotDB = new SlotDB(Connection::Connect());
$slots = $slotDB->getAllSlots();
$slotID = filter_input(INPUT_GET, 'slot_id', FILTER_VALIDATE_INT);
$slotSelecionado = null;

if ($slotID !== false && $slotID !== null && $slotID > 0) {
  $slotSelecionado = $slotDB->getSlotByID($slotID);
}

$topicoPadrao = '';
if ($usuarioLogado && method_exists($_SESSION['obj_user'], 'getDefaultReservationTopic')) {
  $topicoPadrao = $_SESSION['obj_user']->getDefaultReservationTopic();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href=""><!--LOGO DO SITE-->
    <title>SARENH</title>
    <link href="index_style.css" rel="stylesheet">
  </head>
  <body class="scroll">
  
    <a href="login/login.php">login</a><br>
    <a href="register/index.php">Register</a>
    <a href="../src/pcs_EndSession.php">logout</a>
    
    <?php if ($usuarioLogado && $_SESSION['obj_user']->getCategory() === "coordenador"): ?>
      <a href="administration/home/home.php">administrar</a>
    <?php endif; ?>
    
    <?php if ($usuarioLogado): ?>
      <img src="img/logado.png" alt="logado">
    <?php else: ?>
      <img src="img/nologado.png" alt="não logado">
    <?php endif; ?>

    <main class="main-container<?= $slotSelecionado !== null ? ' has-selection' : '' ?>">
      <section class="slots-section">
        <div class="section-heading">
          <div>
            <span class="section-number">01</span>
            <h2>ESPAÇOS DISPONÍVEIS</h2>
          </div>
          <span class="section-label">SELECIONE UMA SALA</span>
        </div>
        <div class="slot-grid">
          <?php foreach ($slots as $slot): ?>
            <form method="POST" action="../src/pcs_InterestSlot.php" class="slot-card-form">
              <input type="hidden" name="slot_id" value="<?= htmlspecialchars((string) $slot->getId(), ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="slot-card">
                <div class="slot-card-top">
                  <span class="slot-number">#<?= htmlspecialchars((string) $slot->getId(), ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="slot-arrow">↗</span>
                </div>
                <div class="slot-card-content">
                  <span class="slot-small-title">ESPAÇO</span>
                  <h3><?= htmlspecialchars($slot->getInstitutionalName(), ENT_QUOTES, 'UTF-8') ?></h3>
                </div>
                <div class="slot-card-bottom">
                  <span>VER ESPAÇO</span>
                  <span>→</span>
                </div>
              </button>
            </form>
          <?php endforeach; ?>
        </div>
      </section>

      <?php if ($slotSelecionado !== null): ?>
        <section class="slot-info">
          <div class="selected-slot-header">
            <div>
              <span class="selected-label">ESPAÇO SELECIONADO</span>
              <h2><?= htmlspecialchars($slotSelecionado->getInstitutionalName(), ENT_QUOTES, 'UTF-8') ?></h2>
              <p class="slot-name"><?= htmlspecialchars($slotSelecionado->getName(), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          </div>

          <div class="description-card">
            <span class="card-label">DESCRIÇÃO</span>
            <p><?= htmlspecialchars($slotSelecionado->getDescription(), ENT_QUOTES, 'UTF-8') ?></p>
          </div>

          <div class="reserved-section">
            <div class="section-heading small-heading">
              <div>
                <span class="section-number">02</span>
                <h2>HORÁRIOS RESERVADOS</h2>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="reservation-table">
                <thead>
                  <tr>
                    <th>USUÁRIO</th>
                    <th>INÍCIO</th>
                    <th>TÉRMINO</th>
                    <th>MOTIVO</th>
                    <th>DATA</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="usuario">
                      <div class="user-avatar">JS</div>
                      <div class="user-info">
                        <strong>João da Silva</strong>
                        <span>joao@email.com</span>
                      </div>
                    </td>
                    <td>14:00</td>
                    <td>16:00</td>
                    <td>Reunião do projeto</td>
                    <td>xx/xx/xxxx</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <?php if ($usuarioLogado): ?>
            <div class="reservation-section">
              <div class="section-heading small-heading">
                <div>
                  <span class="section-number">03</span>
                  <h2>RESERVAR HORÁRIO</h2>
                </div>
              </div>
              <form method="POST" action="../src/pcs_SlotReservation.php" class="reservation-form">
                <input type="hidden" name="slot_id" value="<?= htmlspecialchars((string) $slotSelecionado->getId(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="user_email" value="<?= htmlspecialchars($_SESSION['obj_user']->getEmail(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-group">
                  <label for="topic">TÓPICO</label>
                  <input type="text" id="topic" name="topic" value="<?= htmlspecialchars($topicoPadrao, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label for="start_time">HORA DE INÍCIO</label>
                    <input type="time" id="start_time" name="start_time" required>
                  </div>
                  <div class="form-group">
                    <label for="end_time">HORA DE TÉRMINO</label>
                    <input type="time" id="end_time" name="end_time" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="reason">MOTIVO</label>
                  <input type="text" id="reason" name="reason" required>
                </div>
                <button type="submit" class="reserve-button">RESERVAR HORÁRIO <span>→</span></button>
              </form>
            </div>
          <?php else: ?>
            <div class="login-warning">
              <strong>VOCÊ NÃO ESTÁ LOGADO</strong>
              <p>Faça login para poder reservar este espaço.</p>
              <a href="login/login.php" class="warning-button">FAZER LOGIN →</a>
            </div>
          <?php endif; ?>
        </section>
      <?php endif; ?>
    </main>
  </body>
</html>
