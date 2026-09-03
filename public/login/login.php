<!DOCTYPE html>
<html lang="br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Página de Login</title>
</head>
<body>
<div class="login-container">
    <h1>Login</h1>
<div>
<form method="POST" action="../../src/data/pcs_login.php">
    <input id="username" type="text" name="username" placeholder="Usuário" required>
    <input id="password" type="password" name="password" placeholder="Senha" required>
    <button id="login-btn" type="submit">Entrar</button>
</form>
<footer>
    <p>Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
</footer>
</div>
</div>
</body>
</html>