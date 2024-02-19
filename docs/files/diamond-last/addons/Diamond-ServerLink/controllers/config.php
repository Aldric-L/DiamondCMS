<?php 
$config_serveurs = $cm->getConfig();

if (isset($param[2]) && is_numeric($param[2]) && array_key_exists(intval($param[2]), $config_serveurs)){
    $serverid = $param[2];
    $controleur_def->loadJSAddon(LINK  . "addons/Diamond-ServerLink/views/js/diagnostic.js");
    $controleur_def->loadJSAddon(LINK  . "addons/Diamond-ServerLink/views/js/config.js");
    $controleur_def->loadViewAddon(ROOT . "addons/Diamond-ServerLink/views/config.serveur.php", true, false, "Configuration de " . $config_serveurs[$param[2]]['name']);
    die;
}

$controleur_def->loadViewAddon(ROOT . "addons/Diamond-ServerLink/views/config.gen.php", true, false, "Configuration des serveurs");