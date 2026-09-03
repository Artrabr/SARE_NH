<?php

function FormDataCheck($PostData, $RequiredFields){
    foreach($RequiredFields as $Field){
        if(!isset($PostData[$Field]) || empty($PostData[$Field])){
            return false;
        }
    }
    return true;
}

function ConnectMYSQL(){

}

function CompareInfo($Input, $InputMYSQL){
//input é a variavel que contem algum dado de login do usuario 
//inputMYSQL é o valor que vc precisa pra pegar o valor do dado cadastrado no banco

}

function DisconnectMYSQL(){

}

//-------------------------
//         CÓDIGO
//-------------------------

$date = ['chegada1','chegada2','...'] //array com os nomes dos inputs do formulario de login *QUE SAO OBRIGATORIOS*

if(FormDataCheck($_POST, $date)){
    ConnectMYSQL();

    $Login = $_POST['Login']; //pega o login do usuário
    $Password = $_POST['Password']; //pega a senha do usuário

    CompareInfo($Login, 'user_login'/*VERIFICAR O SE ESTÁ CORRETO O VALOR COM O BANCO*/);
    CompareInfo($Password, 'user_password'/*VERIFICAR O SE ESTÁ CORRETO O VALOR COM O BANCO*/);

    DisconnectMYSQL();
    exit();
    header("Location: ../index.php?success=1");
}

header("Location: ../index.php?error=1");
?>