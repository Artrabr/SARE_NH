<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="../ifBrutal.css">
    <title>Página de Registro</title>
</head>
<body>

    <section class="login-container">
        <img src="../midia/img/ifsullogo/ifsul-logo.png" alt="logo if">
        <h1>Registro</h1>
        <form method="POST" action="pcs_Register.php">
            <label for="username" class="labels">Username:</label>
            <input id="username" type="text" name="username" placeholder="Digite seu username" required>

            <label for="email" class="labels">Email:</label>
            <input id="email" type="text" name="email" placeholder="Digite seu email" required>

            <label for="password" class="labels">Senha:</label>
            <input id="password" type="password" name="password" placeholder="Crie sua senha" required>

            <label for="category" class="labels">Categoria:</label>
            <select id="category" name="category" required>
                <option value="">Selecione sua categoria</option>
                <option value="professor">Professor</option>
                <option value="coordenador">Coordenador</option>
                <option value="servidor">Servidor</option>
                <option value="diretor">Diretor</option>
            </select>

            <button id="login-btn" type="submit">Registar-se</button>
        </form>
    </section>
</body>
</html>
