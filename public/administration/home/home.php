<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
</head>
<body>
    <h1>Bem vindo <?=$_SESSION['username']?>!</h1>
    <p>Esta é a página de administração</p>
    
    <div> <!--atalhos iniciais-->
        <ul>
            <li><a href="users.php">Gerenciar Horarios</a></li>
            <li><a href="users.php">Gerenciar Registros de professores</a></li>
            <li><a href="settings.php">Configurações</a></li>
        </ul>
    </div>

    <nav> <!--icones-->
        <ul>
            <li><a href="users.php">Gerenciar Usuários</a></li>
            <li><a href="users.php">Gerenciar Registros de professores</a></li>
            <li><a href="settings.php">Configurações</a></li>
        </ul>
    </nav>
</body>
</html>