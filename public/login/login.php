<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Página de Login</title>
</head>
<body>

    <div class="login-container">
        <h1>Login</h1>
        <form method="POST" action="../../src/data/pcs_login.php">
            <label for="username" class="labels">Nome de Usuário:</label>
            <input id="username" type="text" name="username" placeholder="Usuário" required>

            <label for="password" class="labels">Senha:</label>
            <input id="password" type="password" name="password" placeholder="Senha" required>

            <button id="login-btn" type="submit">Entrar</button>
        </form>

        <footer>
            <p>Não tem uma conta? <a style="text-decoration: none; font-weight: bold; color: #20a732" href="register.php">Cadastre-se</a></p>
        </footer>
    </div>
</body>
</html>