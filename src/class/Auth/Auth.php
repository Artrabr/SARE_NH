<?php

require_once __DIR__ . '/../../../bootstrap.php';

class Auth
//autoriza acessar a pagina, caso nao te redireciona para o index de sua escolha
{
    public static function requireCategory(string $category, string $redirect = 'PUBLIC_URL'): void
    {
        if (
            !isset($_SESSION['obj_user']) ||
            $_SESSION['obj_user']->getCategory() !== $category //escolhe a categoria que pode acessar
        ) {
            header('Location: ' . $redirect . 'index.php'); //redireciona, para alterar é so adicionar
            exit();                                         //baseado no local a pasta que possui index
        }
    }
}