<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}


// On récupère la config par l'API
$controleur_def->loadModel('api.class');
$controleur_def->loadModel('API/theme.class');
$themeAPI = new theme($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, $_SESSION['user']->getLevel());
$cur_theme_conf = json_decode($themeAPI->get_themeConf(), true)['Return'];

$controleur_def->loadJS('admin/theme');
$controleur_def->loadViewAdmin('admin/config/theme', 'accueil', 'Configuration du thème');