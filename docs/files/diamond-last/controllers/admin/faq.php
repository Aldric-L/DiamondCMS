<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() < 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

$config = $Serveur_Config;
$faq = simplifySQL\select($controleur_def->bddConnexion(), false, "d_faq", "*");
$controleur_def->loadJS("admin/faq");
$controleur_def->loadViewAdmin('admin/config/faq', 'accueil', 'Gestion de la FAQ');