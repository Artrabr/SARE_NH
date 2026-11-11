<?php
//==----== NOTA:

//  o navegatorTitle ele ta pegando o nome da pessoa no perfil,
// mas a ideia dessa pagina é exibir o perfil de qualquer pessoa,
// então o ideal é que o titulo seja o nome da pessoa que está tendo
// seu perfil exibido, e não o nome da pessoa logada. Creio eu que o 
// melhor é usar um parametro GET pra pegar o id do perfil exibido
// pra criar um objeto do tipo User e pegar o nome dele pra exibir no titulo.

//  Salvo esse usuario em $_SESSION['obj_perfil'] pra usar ele em conjunto
// do user e não substituir o user logado, que é o $_SESSION['obj_user'].

//  Lembrar de falar com o yuri sobre a 'conecção' com os links do SUAP
// da escola, essa nota pode ser apagada dps do fim do projeto.

//-----------------------------------=|=-----------------------------------//


    require_once __DIR__ . '/../../../bootstrap.php';
    require_once CLASS_PATH . 'User/UserDB.php';
    require_once CLASS_PATH . 'Teacher/TeacherDB.php';
    require_once DATA_PATH . 'Connection.php';

    $user = $_SESSION['obj_user'] ?? null;
    $profileId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    $profile = null;
    $errorMessage = null;

    if ($profileId === false || $profileId === null || $profileId < 1) {
        http_response_code(400);
        $errorMessage = 'O identificador do perfil é inválido.';
    } else {
        if($user !== null && $profileId == $user->getId()){
            $profile = $user;
        }

        $pdo = Connection::Connect();
        $profileDB = new UserDB($pdo);
        $profile = $profileDB->getUserByID($profileId);

        if ($profile !== null && $profile->getCategory() === 'professor') {
            $teacherDB = new TeacherDB($pdo);
            $profile = $teacherDB->getTeacherByID($profileId) ?? $profile;
        }

        if ($profile === null) {
            http_response_code(404);
            $errorMessage = 'Perfil não encontrado.';
        }
    }

    $navegatorTitle = $profile !== null
        ? $profile->getName() . ' | perfil'
        : 'Perfil';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    <?= htmlspecialchars($navegatorTitle, ENT_QUOTES, 'UTF-8') ?>
    </title>
</head>
<body>
    <?php
//===================================PAGINA DO PERFIL===========================
    if ($errorMessage !== null):
    ?>
    <h1><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php
    elseif($user === null || $user->getId() != $profileId):
    ?>
    <div>
        <img src="" alt="Foto de perfil">
        <div>
            <h1><?= htmlspecialchars($profile->getName(), ENT_QUOTES, 'UTF-8') ?></h1>
            <h3><?= htmlspecialchars($profile->getCategory(), ENT_QUOTES, 'UTF-8') ?></h3>
            <?php if($profile instanceof Teacher): ?>
                <p><?= htmlspecialchars($profile->getSubject(), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <h2>Reservas</h2>

    </div>
    <?php
    else:
//===================================PAGINA DO USUARIO===========================
    ?>
    <div>
        <img src="" alt="Foto de perfil">
        <div>
            <input name="name" style="<?php #h1 ?>" value="<?= htmlspecialchars($profile->getName(), ENT_QUOTES, 'UTF-8') ?>">
            <input name="category" style="<?php #h3 ?>" value="<?= htmlspecialchars($profile->getCategory(), ENT_QUOTES, 'UTF-8') ?>">
            <?php if($profile instanceof Teacher): //man, esssa foi a coisa mais genial q eu ja fiz ta KKKKKKKKKKK?>
                <input name="category" style="<?php #p ?>" value="<?= htmlspecialchars($profile->getSubject(), ENT_QUOTES, 'UTF-8') ?>">
            <?php
                endif;    
            ?>
        </div>
    </div>
    <div>
        <h2>Reservas</h2>

    </div>
    <?php
    endif;
    ?>
    <a href="../../pcs_destroySession.php">deslogar</a>
</body>
</html>