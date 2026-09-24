<?php
require_once __DIR__ . '/../../../src/data/Connect.php';
require_once __DIR__ . '/../../../src/class/user/UserDB.php';

$users = (new UserDB(Connection::Connect()))->getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lista de registrados</title>
</head>
<body>
    <img src="lista.png" alt="whatsthat">
    <main>
        <section>
            <?php
            
            ?>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($user->getCategory(), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
            
            ?>
        </section>
    </main>
</body>
</html>