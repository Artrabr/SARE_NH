<?php
    require_once __DIR__ . '/../../bootstrap.php';
    require_once CLASS_PATH . 'User/User.php';
    require_once CLASS_PATH . 'Teacher/Teacher.php';
    require_once CLASS_PATH . 'Auth/Auth.php';
    
    Auth::requireCategory('coordenador');

    $name = $_SESSION['obj_user']->getName();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
</head>
<body>
    <h1>Bem vindo <?=htmlspecialchars($name)?>!</h1>
    <p>Esta é a página de administração</p>

    <div> <!--atalhos iniciais-->
        <ul>
            <li><a href="timelists/index.php">Gerenciar horários</a></li>
            <li><a href="classroomTimeList/classroomTimeList.php">Gerenciar registros de salas</a></li>
            <li><a href="clientList/clientList.php">Gerenciar usuários e professores</a></li>
        </ul>
    </div>

    <nav> <!--icones-->
        <ul>
            <li><a href="clientList/clientList.php">Gerenciar usuários e professores</a></li>
            <li><a href="classroomTimeList/classroomTimeList.php">Gerenciar registros de salas</a></li>
            <li><a href="timelists/index.php">Gerenciar horários</a></li>
        </ul>
    </nav>
</body>
</html>