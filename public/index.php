<?php
//-------------requires----------------------------//
require_once __DIR__ . '/../bootstrap.php';
require_once CLASS_PATH . 'User/User.php';
require_once CLASS_PATH . 'Teacher/Teacher.php';
require_once CLASS_PATH . 'Slot/SlotDB.php';
require_once DATA_PATH . 'Connection.php';
//================================================//

//----------------Declaracao de topo-----------------//
$isUserLoggedIn = isset($_SESSION['obj_user']);
$slotDb = new SlotDB(Connection::Connect());
$slots = $slotDb->getAllSlots();
$slotId = filter_input(INPUT_GET, 'slot_id', FILTER_VALIDATE_INT);
$selectedSlot = null;
$admin = 'coordenador';
//---------------------------------------------------//

if ($slotId !== false && $slotId !== null && $slotId > 0) { //verifica se nao tem jeito nem um de estar null, mas de jeito nem um mesmo, impressionante
  $selectedSlot = $slotDb->getSlotByID($slotId);
}

//------------------------------definir o topico padrao da aula-------------------------------//
$defaultTopic = ''; 
//seguranca, caso nao seja carregado na classe user --> nao sei se nao da pra remover, mas fica pra dps
if ($isUserLoggedIn && method_exists($_SESSION['obj_user'], 'getDefaultReservationTopic')) {
  $defaultTopic = $_SESSION['obj_user']->getDefaultReservationTopic(); //define o topico baseado na classe
}
//============================================================================================//

//----------------Manipulação-do-dado-em-php-do-svg-----------------//
$selecionado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content']) && is_string($_POST['content'])) {
  $selecionado = trim($_POST['content']);
  if ($selecionado !== '') {
    $selectedSlot = $slotDb->getSlotByInstitutionalName($selecionado);
  }
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
    <script src="https://cdn.jsdelivr.net/npm/svg-pan-zoom@3.6.1/dist/svg-pan-zoom.min.js"></script>
  </head>



<body class="map-page">

    <nav class="site-nav" aria-label="Navegação principal">
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
          <a href="#"></a> <!--ir para a pagina do usuario-->
        <?php
        endif;
        ?>
      </div>
    </nav>
    
    <main class="main-container<?= $selectedSlot !== null ? ' has-selection' : '' ?>">
      <section class="slots-section">
        <div class="section-heading">
          <div>
            <h2>ESPAÇOS DISPONÍVEIS</h2>
            <p>Clique em uma sala da planta para consultar os detalhes e solicitar uma reserva.</p>
          </div>
          <span class="section-label">SELECIONE UMA SALA</span>
        </div>
      </section>

      <?php if ($selectedSlot !== null): ?>
        <section class="slot-info">
          <div class="selected-slot-header">
            <div>
              <span class="selected-label">ESPAÇO SELECIONADO</span>
              <h2><?= htmlspecialchars($selectedSlot->getInstitutionalName(), ENT_QUOTES, 'UTF-8') ?></h2>
              <p class="slot-name"><?= htmlspecialchars($selectedSlot->getName(), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <a class="close-button" href="index.php" aria-label="Voltar ao mapa">×</a>
          </div>

          <div class="description-card">
            <span class="card-label">DESCRIÇÃO</span>
            <p><?= htmlspecialchars($selectedSlot->getDescription(), ENT_QUOTES, 'UTF-8') ?></p>
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
          </div>

          <?php if ($isUserLoggedIn): ?>
            <div class="reservation-section">
              <div class="section-heading small-heading">
                <div>
                  <span class="section-number">03</span>
                  <h2>RESERVAR HORÁRIO</h2>
                </div>
              </div>
              <form method="POST" action="pcs_SlotReservation.php" class="reservation-form">
                <input type="hidden" name="slot_id" value="<?= htmlspecialchars((string) $selectedSlot->getId(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-group">
                  <label for="topic">TÓPICO</label>
                  <input type="text" id="topic" name="topic" value="<?= htmlspecialchars($defaultTopic, ENT_QUOTES, 'UTF-8') ?>" required>
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
      <?php elseif ($selecionado !== null): ?>
        <section class="slot-info">
          <p>O espaço selecionado não foi encontrado.</p>
        </section>
      <?php endif; ?>
    </main>
    
<svg
   width="100%"
   height="100%"
   viewBox="0 0 210 297"
   version="1.1"
   preserveAspectRatio="xMidYMid meet"
   id="campus-map"
   data-selected-slot="<?= $selectedSlot !== null ? htmlspecialchars($selectedSlot->getInstitutionalName(), ENT_QUOTES, 'UTF-8') : '' ?>"
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
       inkscape:label="convivnecia" />
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
    <nav class="map-controls" aria-label="Controles da planta">
      <button type="button" id="zoom-in" aria-label="Aumentar zoom">+</button>
      <button type="button" id="zoom-out" aria-label="Diminuir zoom">−</button>
      <button type="button" id="reset-map" aria-label="Centralizar planta">⌖</button>
    </nav>
    <div class="map-hint">
      <strong>Navegação</strong><br>
      Scroll: zoom · Arraste: movimentar<br>
      Clique em uma sala para selecioná-la
    </div>
<!--regiao-- MANIPULAÇÃO DO SVG -->
  <!------------Recebimento de dados do js > php--------------->
    <form id="formSelection" action="index.php" method="post">
        <input type="hidden" name="content" id="Selected">
    </form>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const map = document.getElementById("campus-map");
        const selectionInput = document.getElementById("Selected");
        const selectionForm = document.getElementById("formSelection");
        const rooms = Array.from(map.querySelectorAll("rect"))
          .filter((room) => room.hasAttribute("inkscape:label"));
        const selectedSlot = map.dataset.selectedSlot.toLowerCase();

        rooms.forEach((room) => {
          room.classList.add("room");
          room.setAttribute("tabindex", "0");
          room.setAttribute("role", "button");

          const label = room.getAttribute("inkscape:label");
          room.setAttribute("aria-label", `Selecionar ${label}`);
          if (label.toLowerCase() === selectedSlot) {
            room.classList.add("selected");
          }

          const selectRoom = () => {
            selectionInput.value = label;
            selectionForm.requestSubmit();
          };

          room.addEventListener("click", selectRoom);
          room.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
              event.preventDefault();
              selectRoom();
            }
          });
        });

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
          dblClickZoomEnabled: true,
          pinchZoomEnabled: true,
          minZoom: 0.5,
          maxZoom: 12,
          zoomScaleSensitivity: 0.3,
          fit: true,
          center: true
        });

        document.getElementById("zoom-in").addEventListener("click", () => navigation.zoomIn());
        document.getElementById("zoom-out").addEventListener("click", () => navigation.zoomOut());
        document.getElementById("reset-map").addEventListener("click", () => {
          navigation.resetZoom();
          navigation.fit();
          navigation.center();
        });
      });
    </script>
<!--endregiao-->
  </body>
</html>
