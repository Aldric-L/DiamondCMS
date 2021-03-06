<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

// Si l'on passe en mode modification des fichiers config (requettes POST via AJAX)
if (isset($_POST['content'])){
    $fp = fopen (ROOT . "config/m-legal.ftxt", "w");
    if (!$fp){
        $controleur_def->addError(111);
    }
    fseek ($fp, 0);
    fputs ($fp, $_POST['content']);
    fclose ($fp);
}

$current = file_get_contents(ROOT . "config/m-legal.ftxt");
$controleur_def->loadViewAdmin('admin/config/m-legal', 'accueil', 'Configuration du CMS - Page Mentions légales');