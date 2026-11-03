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


    require_once __DIR__ . '/../../bootstrap.php';
    require_once CLASS_PATH . 'User/User.php';
    require_once CLASS_PATH . 'Teacher/Teacher.php';

    if(!isset($_SESSION['obj_user'])){
        $navegatorTitle = "Perfil";
    }else{
        $navegatorTitle = $_SESSION['obj_user']->getName();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    <?= $navegatorTitle ?>
    </title>
</head>
<body>
</body>
</html>