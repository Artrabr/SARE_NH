<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../register/register.css">
    <link rel="stylesheet" href="../ifBrutal.css">
    <title>Página de Login</title>
</head>
<body>

    <section class="login-container">
        <img src="../midia/img/ifsullogo/ifsul-logo.png" alt="Logo do IFSul">
        <h1>Login</h1>
        <form method="POST" action="../../src/pcs_login.php">
            <label for="email" class="labels">Email:</label>
            <input id="email" type="text" name="email" placeholder="email" required>

            <label for="password" class="labels">Senha:</label>
            <input id="password" type="password" name="password" placeholder="Senha" required>

            <button id="login-btn" type="submit">Entrar</button>
        </form>

        <footer>
            <p>Não tem uma conta? <a style="text-decoration: none; font-weight: bold; color: #20a732" href="../register/index.php">Cadastre-se</a></p>
        </footer>
    </section>
</body>
</html>
