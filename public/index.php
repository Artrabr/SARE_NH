<?php
require_once __DIR__ . '/../src/class/user/User.php';
require_once __DIR__ . '/../src/class/Slot/SlotDB.php';
require_once __DIR__ . '/../src/data/Connection.php';
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>teste abuble</title>
    <link href="index_style.css" rel="stylesheet">
  </head>
  <body class="scroll">
  
    <a href="login/login.php">login</a><br>
    <a href="register/index.php">Register</a>
    
    <?php if(isset($_SESSION['obj_user']) && $_SESSION['obj_user']->getCategory() == "coordenador"):?>
      <a href="administration/home/home.php">administrar</a>
    <?php endif;?>
    
    <?php if(isset($_SESSION['obj_user'])):?>
      <img src="img/logado.png" alt="logado">
    <?php else:?>
      <img src="img/nologado.png" alt="não logado">
    <?php endif; ?>

    <!--temporario até a implementacao do mapa-->
    <form method="POST" action="../src/pcs_SlotReservation.php">
      <input type="hidden" value="<?= $_SESSION['obj_user']->getEmail()?>">
      <input type="submit" value="B2-02">
    </form>
    
    <!--essa div só será exposta quando o usuario clicar em um slot-->
    <div class="slot_info">
      <?php
        $slotDB = new SlotDB(Connection::Connect());
        $slot = $slotDB->getSlotByID(1); // Replace 1 with the actual slot ID

        $slotName = $slot->getName();
        $slotInstitutionalName = $slot->getInstitutionalName();
        $slotDescription = $slot->getDescription();
      ?>
      <div>
        <h1><strong>Sala <?= htmlspecialchars($slotInstitutionalName) ?></strong></h1>
        <h4><?= htmlspecialchars($slotName) ?></h4>
        <p>Descrição: <span id="slot_description"><?= htmlspecialchars($slotDescription) ?></span></p>
      </div>

      <div>
      <h1>Horários Reservados</h1>

      <table>
          <tbody>
              <tr>
                  <td class="usuario">
                      <img src="usuario.jpg" alt="Foto do usuário">

                      <div>
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

      <form method="POST" action="../src/pcs_SlotReservation.php">
        <h1>Reservar horário:</h1>
        <input type="hidden" name="slot_id" value="<?= htmlspecialchars($slot->getId()) ?>">
        <input type="hidden" name="user_email" value="<?= htmlspecialchars($_SESSION['obj_user']->getEmail()) ?>">
        <label for="topic">Tópico:</label>
        <input type="text" name="topic" value="<?= htmlspecialchars($_SESSION['obj_user']->getDefaultReservationTopic()) ?>" required>

        <label for="start_time">Hora de início:</label>
        <input type="time" id="start_time" name="start_time" required>
        <label for="end_time">Hora de término:</label>
        <input type="time" id="end_time" name="end_time" required>
        <label for="reason">Motivo:</label>
        <input type="text" id="reason" name="reason" required>

        <input type="submit" value="Reservar">
      </form>

      <div>
        <img src="" alt="foto do usuario">
        <div>
          <strong>Nome do Usuário</strong>
          <span>email@dominio.com</span>
        </div>
      </div>
    </div>

    <div class="svg_background">

      <object data="./stable_optimized_relative_4_0.svg" type="image/svg+xml"></object>

    </div>

  </body>
</html>
