<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

//Récupération de la configuration de la base de données
$bddconfig = $controleur_def->getBDD()->getConfig();
$conf_mce = cleanIniTypes(parse_ini_file(ROOT . "config/tinymce.ini", true));

//On liste aussi les images disponibles dans le dossier img et lisibles avec aucune autorisation
$img_available = Manager::listAvailableFiles(ROOT . 'views/uploads/img', 1);

$controleur_def->loadJS('admin/config');
$controleur_def->loadViewAdmin('admin/config/config', 'accueil', 'Configuration du CMS');