<?php
//-------------requires----------------------------//
require_once __DIR__ . '/../bootstrap.php';
require_once CLASS_PATH . 'User/User.php';
require_once CLASS_PATH . 'Teacher/Teacher.php';
//================================================//

//----------------Declaracao de topo-----------------//
$isUserLoggedIn = isset($_SESSION['obj_user']);
$initialSlotId = filter_input(INPUT_GET, 'slot_id', FILTER_VALIDATE_INT);
$initialSlotId = $initialSlotId !== false && $initialSlotId !== null && $initialSlotId > 0
  ? (string) $initialSlotId
  : '';
$hasInitialSelection = $initialSlotId !== '';
$admin = 'coordenador';
//---------------------------------------------------//

//------------------------------definir o topico padrao da aula-------------------------------//
$defaultTopic = ''; 
//seguranca, caso nao seja carregado na classe user --> nao sei se nao da pra remover, mas fica pra dps
if ($isUserLoggedIn && method_exists($_SESSION['obj_user'], 'getDefaultReservationTopic')) {
  $defaultTopic = $_SESSION['obj_user']->getDefaultReservationTopic(); //define o topico baseado na classe
}
//============================================================================================//

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore_information'])) {
  unset($_SESSION['slots_section_shown'], $_SESSION['map_hint_shown']);
}

$showSlotsInfo = !$hasInitialSelection && empty($_SESSION['slots_section_shown']);
$showMapHint = !$hasInitialSelection && empty($_SESSION['map_hint_shown']);

if ($showSlotsInfo) {
  $_SESSION['slots_section_shown'] = true;
}

if ($showMapHint) {
  $_SESSION['map_hint_shown'] = true;
}
//===================================================================//
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href=""><!--LOGO DO SITE-->
    <title>SARENH</title>
    <link href="public_css/index_style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/svg-pan-zoom@3.6.1/dist/svg-pan-zoom.min.js"></script>
  </head>
  
<body class="map-page">

    <?php if (isset($_GET['horario_invalido']) || isset($_GET['horario_ocupado'])): ?>
      <div class="reservation-notification" role="alert" aria-live="assertive">
        <?= isset($_GET['horario_ocupado'])
          ? 'Esse horário já está reservado para essa sala. Escolha outro.'
          : 'O horário de término deve ser posterior ao horário de início.' ?>
      </div>
    <?php endif; ?>

    <nav id="site-nav" class="site-nav" aria-label="Navegação principal"<?= $hasInitialSelection ? ' hidden' : '' ?>>
      <a class="site-brand" href="index.php">SARENH</a>

      <div class="site-links">
        <?php
        if(!$isUserLoggedIn):
        ?>
          <a href="login/login.php">Login</a>
          <a href="register/index.php">Cadastro</a>
        <?php
        elseif($isUserLoggedIn && $_SESSION['obj_user']->isAdmin()):
        ?>
          <a href="administration/index.php">Administrar</a>
        <?php
        else:
        ?>
          <a href="user/perfil/index.php?id=<?= $_SESSION['obj_user']->getId()?>"><i class="fa-solid fa-user" class="icon"></i></a>
        <?php
        endif;
        ?>
      </div>
    </nav>
    
    <main id="main-container" class="main-container<?= $hasInitialSelection ? ' has-selection' : '' ?>">
      <?php if ($showSlotsInfo): ?>
      <section class="slots-section">
        <div class="section-heading">
          <div>
            <h2>ESPAÇOS DISPONÍVEIS</h2>
            <p>Clique em uma sala da planta para consultar os detalhes e solicitar uma reserva.</p>
          </div>
          <span class="section-label">SELECIONE UMA SALA</span>
        </div>
      </section>
      <?php endif; ?>

      <template id="slot-info-template">
        <section class="slot-info" aria-label="Informações do espaço selecionado">
          <div class="selected-slot-header">
            <div>
              <span class="selected-label">ESPAÇO SELECIONADO</span>
              <h2 data-slot-institutional-name></h2>
              <p class="slot-name" data-slot-name></p>
            </div>
            <button type="button" class="close-button" data-close-slot aria-label="Voltar ao mapa">×</button>
          </div>

          <div class="description-card">
            <span class="card-label">DESCRIÇÃO</span>
            <p data-slot-description></p>
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
            <button type="button" class="btn btn-primary" data-close-slot>Ver disponibilidades</button>
          </div>
          
          <!-----------------Se o usuario estiver logado, ele podera reservar o horario tudo continua no bloco----------------->
          <?php if ($isUserLoggedIn): ?>
            <div class="reservation-section">
              <div class="section-heading small-heading">
                <div>
                  <span class="section-number">03</span>
                  <h2>RESERVAR HORÁRIO</h2>
                </div>
              </div>
              <form method="POST" action="pcs_SlotReservation.php" class="reservation-form">
                <input type="hidden" name="slot_id" value="">
                <div class="form-group">
                  <label for="topic">TÓPICO</label>
                  <input type="text" id="topic" name="topic" value="<?= htmlspecialchars($defaultTopic, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

      <!-----------------Bloco de horario e data----------------->
      <div class="form-row">
        <div class="form-group">
          <label for="start_time">HORA DE INÍCIO</label>
            
          <select id="start_time" name="start_time" required>
            <option value="">Selecione</option>
            <?php

            $start = strtotime('07:30');
            $limit = strtotime('22:00');
            $duration = 45 * 60;

            while ($start + $duration <= $limit) {
              $end = $start + $duration;
              $startTime = date('H:i', $start);
              $endTime = date('H:i', $end);

            ?>
              <option value="<?= $startTime ?>"><?= $startTime ?></option>
            <?php
              $start = $end;
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="end_time">HORA DE TÉRMINO</label>
          <select id="end_time" name="end_time" required disabled>
            <option value="">Selecione primeiro o horário de início</option>
          </select>
        </div>
      </div>
      <!---------------------------------------------------------->
                <div class="form-group">
                  <label for="reservation_date">DATA</label>
                  <input type="date" id="reservation_date" name="reservation_date" required>
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
      </template>
      <div id="slot-feedback" class="slot-feedback" role="status" aria-live="polite" hidden></div>
    </main>
    
<svg
   width="100%"
   height="100%"
   viewBox="0 0 210 297"
   version="1.1"
   preserveAspectRatio="xMidYMid meet"
   id="campus-map"
   data-selected-slot-id="<?= htmlspecialchars($initialSlotId, ENT_QUOTES, 'UTF-8') ?>"
   inkscape:export-filename="mapacampus.svg"
   inkscape:export-xdpi="96"
   inkscape:export-ydpi="96"
   xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
   xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:svg="http://www.w3.org/2000/svg">
  <sodipodi:namedview
     id="namedview1"
     pagecolor="#ffffff"
     bordercolor="#000000"
     borderopacity="0.25"
     inkscape:showpageshadow="2"
     inkscape:pageopacity="0.0"
     inkscape:pagecheckerboard="0"
     inkscape:deskcolor="#d1d1d1"
     inkscape:document-units="mm">
    <inkscape:page
       x="0"
       y="0"
       width="210"
       height="297"
       id="page2"
       margin="0"
       bleed="0" />
  </sodipodi:namedview>
  <defs
     id="defs1" />
  <g
     inkscape:label="Camada 1"
     inkscape:groupmode="layer"
     id="layer1">
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect5"
       width="47.659851"
       height="28.798326"
       x="16.377323"
       y="45.819702"
       inkscape:label="convivencia" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect6"
       width="47.883484"
       height="95.506737"
       x="16.394888"
       y="77.550423"
       inkscape:label="ginasio" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect7"
       width="23.941742"
       height="28.88623"
       x="88.480347"
       y="46.061829"
       inkscape:label="lab1" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect8"
       width="22.640558"
       height="28.88623"
       x="114.76421"
       y="46.061829"
       inkscape:label="lab2" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect9"
       width="47.623249"
       height="27.845285"
       x="89.000824"
       y="90.562233"
       inkscape:label="b4-05" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect10"
       width="47.36301"
       height="23.941742"
       x="89.000824"
       y="121.0099"
       inkscape:label="b4-04" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect11"
       width="31.228359"
       height="20.298433"
       x="166.03078"
       y="89.781532"
       inkscape:label="b2-04" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect12"
       width="31.748831"
       height="19.777962"
       x="165.5103"
       y="115.54492"
       inkscape:label="b2-03" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect13"
       width="32.009068"
       height="18.476778"
       x="165.77054"
       y="140.78786"
       inkscape:label="b2-02" />
    <rect
       style="fill:#dadada;fill-opacity:1;stroke:#000000;stroke-width:0.964999;stroke-dasharray:none;stroke-opacity:1"
       id="rect14"
       width="32.269302"
       height="18.216541"
       x="166.03078"
       y="162.90793"
       inkscape:label="b2-01" />
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="35.652374"
       y="124.6532"
       id="text14"
       inkscape:label="ginasiotext"><tspan
         sodipodi:role="line"
         id="tspan14"
         style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
         x="35.652374"
         y="124.6532">ginasio</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="30.672613"
       y="60.50037"
       id="text14-8"
       inkscape:label="convivenciatext"><tspan
         sodipodi:role="line"
         id="tspan14-2"
         style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
         x="30.672613"
         y="60.50037">Convivencia</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="90.836632"
       y="61.144257"
       id="text14-8-7"
       inkscape:label="lab01text"><tspan
         sodipodi:role="line"
         id="tspan14-2-1"
         style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
         x="90.836632"
         y="61.144257">laboratorio 1</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="116.04823"
       y="60.972694"
       id="text14-8-7-6"
       inkscape:label="lab2text"><tspan
         sodipodi:role="line"
         id="tspan14-2-1-1"
         style="fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
         x="116.04823"
         y="60.972694">laboratorio 2</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="107.99808"
       y="106.69688"
       id="text15"
       inkscape:label="b4-05text"><tspan
         sodipodi:role="line"
         id="tspan15"
         style="stroke-width:1.865"
         x="107.99808"
         y="106.69688">b4-05</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="106.69689"
       y="131.67958"
       id="text16"
       inkscape:label="b4-04text"><tspan
         sodipodi:role="line"
         id="tspan16"
         style="stroke-width:1.865"
         x="106.69689"
         y="131.67958">b4-04</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="172.53668"
       y="100.19099"
       id="text17"
       inkscape:label="b2-04text"><tspan
         sodipodi:role="line"
         id="tspan17"
         style="stroke-width:1.865"
         x="172.53668"
         y="100.19099">b2-04</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="175.39928"
       y="125.43391"
       id="text18"
       inkscape:label="b2-03text"><tspan
         sodipodi:role="line"
         id="tspan18"
         style="stroke-width:1.865"
         x="175.39928"
         y="125.43391">b2-03</tspan><tspan
         sodipodi:role="line"
         style="stroke-width:1.865"
         x="175.39928"
         y="129.40265"
         id="tspan19" /></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="174.0981"
       y="149.11542"
       id="text20"
       inkscape:label="b2-02text"><tspan
         sodipodi:role="line"
         id="tspan20"
         style="stroke-width:1.865"
         x="174.0981"
         y="149.11542">b2-02</tspan></text>
    <text
       xml:space="preserve"
       style="font-size:3.175px;writing-mode:lr-tb;direction:ltr;fill:#000000;fill-opacity:1;stroke:none;stroke-width:1.865;stroke-dasharray:none;stroke-opacity:1"
       x="176.44023"
       y="174.35834"
       id="text21"><tspan
         sodipodi:role="line"
         id="tspan21"
         style="stroke-width:1.865"
         x="176.44023"
         y="174.35834">b2-01</tspan></text>
  </g>
</svg>
    <?php if ($showMapHint): ?>
    <div class="map-hint">
      <strong>Navegação</strong><br>
      Scroll: zoom · Arraste: movimentar<br>
      Clique em uma sala para selecioná-la
    </div>
    <?php endif; ?> 
    <form id="restoreInformationForm" action="index.php" method="post"<?= $hasInitialSelection ? ' hidden' : '' ?>>
      <input type="hidden" name="restore_information" value="1">
      <button
        type="submit"
        id="restore-information"
        aria-label="Mostrar informações da planta"
        title="Mostrar informações da planta"
        >
        <i class="fa-solid fa-lightbulb" aria-hidden="true"></i>
      </button>
    </form>

    <script>
      //-------------------------seleção de variaveis e adicao da funao room pra cada um
      document.addEventListener("DOMContentLoaded", () => {
        const informationBlocks = Array.from(document.querySelectorAll(
          ".slots-section, .map-hint"
        ));
        const restoreInformationButton = document.getElementById("restore-information");
        const restoreInformationForm = document.getElementById("restoreInformationForm");

        const updateRestoreInformationButton = () => {
          if (restoreInformationButton) {
            const informationIsVisible = informationBlocks.some((block) => block.isConnected);
            restoreInformationButton.classList.toggle("is-visible", !informationIsVisible);
          }
        };

        updateRestoreInformationButton();

        if (restoreInformationForm && restoreInformationButton) {
          restoreInformationForm.addEventListener("submit", (event) => {
            event.preventDefault();
            restoreInformationButton.classList.remove("is-visible");
            window.setTimeout(() => restoreInformationForm.submit(), 250);
          });
        }

        informationBlocks.forEach((block, index) => {
          const dismissBlock = () => {
            if (block.classList.contains("is-dismissing")) {
              return;
            }

            block.classList.add("is-dismissing");
            window.setTimeout(() => {
              block.remove();
              updateRestoreInformationButton();
            }, 1000);
          };

          block.tabIndex = 0;
          block.addEventListener("pointerup", dismissBlock);
          block.addEventListener("click", dismissBlock);
          block.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
              event.preventDefault();
              dismissBlock();
            }
          });

          window.setTimeout(dismissBlock, 10000 + index * 250);
        });

        const map = document.getElementById("campus-map");
        const mainContainer = document.getElementById("main-container");
        const siteNav = document.getElementById("site-nav");
        const slotTemplate = document.getElementById("slot-info-template");
        const slotFeedback = document.getElementById("slot-feedback");
        let currentSlotPanel = null;
        let selectionRequest = 0;

        const initializeReservationForm = (panel) => {
          const startTimeSelect = panel.querySelector("#start_time");
          const endTimeSelect = panel.querySelector("#end_time");

          if (!startTimeSelect || !endTimeSelect) {
            return;
          }

          startTimeSelect.addEventListener("change", () => {
            endTimeSelect.replaceChildren(new Option("Selecione", ""));

            if (!startTimeSelect.value) {
              endTimeSelect.options[0].text = "Selecione primeiro o horário de início";
              endTimeSelect.disabled = true;
              return;
            }

            const [startHour, startMinute] = startTimeSelect.value.split(":").map(Number);
            const startMinutes = startHour * 60 + startMinute;
            const limitMinutes = 22 * 60;
            const minimumDurationMinutes = 45;
            const endTimeStepMinutes = 15;

            for (
              let endMinutes = startMinutes + minimumDurationMinutes;
              endMinutes <= limitMinutes;
              endMinutes += endTimeStepMinutes
            ) {
              const hours = String(Math.floor(endMinutes / 60)).padStart(2, "0");
              const minutes = String(endMinutes % 60).padStart(2, "0");
              const time = `${hours}:${minutes}`;
              endTimeSelect.add(new Option(time, time));
            }

            endTimeSelect.disabled = false;
          });
        };

        const rooms = Array.from(map.querySelectorAll("rect"))
          .filter((room) => room.hasAttribute("inkscape:label"));

        const closeSlotPanel = () => {
          selectionRequest++;
          if (currentSlotPanel) {
            currentSlotPanel.remove();
            currentSlotPanel = null;
          }
          mainContainer.classList.remove("has-selection");
          siteNav.hidden = false;
          restoreInformationForm.hidden = false;
          slotFeedback.hidden = true;
          slotFeedback.textContent = "";
          map.removeAttribute("aria-busy");
          rooms.forEach((room) => room.classList.remove("selected"));
          window.history.replaceState(null, "", "index.php");
          updateRestoreInformationButton();
        };

        const renderSlot = (slot) => {
          const panel = slotTemplate.content.firstElementChild.cloneNode(true);
          panel.querySelector("[data-slot-institutional-name]").textContent = slot.institutionalName;
          panel.querySelector("[data-slot-name]").textContent = slot.name;
          panel.querySelector("[data-slot-description]").textContent = slot.description;
          const reservationSlotId = panel.querySelector('input[name="slot_id"]');
          if (reservationSlotId) {
            reservationSlotId.value = slot.id;
          }
          panel.querySelectorAll("[data-close-slot]").forEach((button) => {
            button.addEventListener("click", closeSlotPanel);
          });

          if (currentSlotPanel) {
            currentSlotPanel.remove();
          }
          currentSlotPanel = panel;
          mainContainer.append(panel);
          mainContainer.classList.add("has-selection");
          siteNav.hidden = true;
          restoreInformationForm.hidden = true;
          informationBlocks.forEach((block) => block.remove());
          rooms.forEach((room) => {
            const label = room.getAttribute("inkscape:label");
            room.classList.toggle(
              "selected",
              label.toLowerCase() === slot.institutionalName.toLowerCase()
            );
          });
          initializeReservationForm(panel);
        };

        const loadSlot = async (parameters) => {
          const requestNumber = ++selectionRequest;
          const endpoint = new URL("api/slot.php", window.location.href);
          Object.entries(parameters).forEach(([key, value]) => {
            endpoint.searchParams.set(key, value);
          });
          map.setAttribute("aria-busy", "true");
          slotFeedback.textContent = "Carregando os dados da sala...";
          slotFeedback.hidden = false;

          try {
            const response = await fetch(endpoint, {
              headers: { Accept: "application/json" }
            });
            const slot = await response.json();

            if (requestNumber !== selectionRequest) {
              return;
            }

            if (!response.ok) {
              slotFeedback.textContent = slot.error || "Não foi possível encontrar esta sala.";
              return;
            }

            renderSlot(slot);
            slotFeedback.hidden = true;
            slotFeedback.textContent = "";

            const pageUrl = new URL(window.location.href);
            pageUrl.search = "";
            pageUrl.searchParams.set("slot_id", slot.id);
            window.history.replaceState(null, "", pageUrl);
          } catch (error) {
            if (requestNumber !== selectionRequest) {
              return;
            }

            console.error("Erro ao carregar os dados da sala.", error);
            slotFeedback.textContent = "Não foi possível carregar os dados da sala. Tente novamente.";
          } finally {
            if (requestNumber === selectionRequest) {
              map.removeAttribute("aria-busy");
            }
          }
        };

        rooms.forEach((room) => {
          room.classList.add("room");
          room.setAttribute("tabindex", "0");
          room.setAttribute("role", "button");

          const label = room.getAttribute("inkscape:label");
          room.setAttribute("aria-label", `Selecionar ${label}`);

          //---------mesmos codigos mas para diferentes dispositivos (qquestaao de acessibilidade)--------------
          const selectRoom = () => {
            loadSlot({ institutional_name: label });
          };

          let touchSlotDelay = 250
          let totalOfTouches = 0;
          let timer;

          const selectRoomTouch = () => { 
//pra evira clique sem querer ele reseta o codigo dps de 250mils
            totalOfTouches++;

            timer = setTimeout(() => {
              totalOfTouches = 0; // Zera se o usuário demorar demais
            }, touchSlotDelay);

            if(totalOfTouches > 1 && timer > 0){
              selectRoom();
            }else if(!timer > 0){ //dps de um tempo x 
              totalOfTouches = 0; //os cliques ficam = 0
            }else{
              return;
            }
          };

          room.addEventListener("touchend", selectRoomTouch);
          room.addEventListener("click", selectRoom);
          room.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
              event.preventDefault();
              selectRoom();
            }
          });
        });

        if (map.dataset.selectedSlotId) {
          loadSlot({ id: map.dataset.selectedSlotId });
        }

        if (typeof svgPanZoom !== "function") {
          console.error("A biblioteca de navegação da planta não foi carregada.");
          return;
        }

        const navigation = svgPanZoom(map, {
          zoomEnabled: true,
          panEnabled: true,
          controlIconsEnabled: false,
          mouseWheelZoomEnabled: true,
          preventMouseEventsDefault: true,
          dblClickZoomEnabled: false,
          pinchZoomEnabled: true,
          minZoom: 0.5,
          maxZoom: 12,
          zoomScaleSensitivity: 0.3,
          fit: true,
          center: true
        });

        const zoomInButton = document.getElementById("zoom-in");
        const zoomOutButton = document.getElementById("zoom-out");
        const resetMapButton = document.getElementById("reset-map");

        if (zoomInButton) {
          zoomInButton.addEventListener("click", () => navigation.zoomIn());
        }
        if (zoomOutButton) {
          zoomOutButton.addEventListener("click", () => navigation.zoomOut());
        }
        if (resetMapButton) {
          resetMapButton.addEventListener("click", () => {
            navigation.resetZoom();
            navigation.fit();
            navigation.center();
          });
        }
      });
        

    </script>
<!--endregiao-->
  </body>
</html>
