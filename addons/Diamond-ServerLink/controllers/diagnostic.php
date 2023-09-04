<?php 
$config_serveurs = $cm->getConfig();

$controleur_def->loadJSAddon(LINK  . "addons/Diamond-ServerLink/views/js/diagnostic.js");
$controleur_def->loadViewAddon(ROOT . "addons/Diamond-ServerLink/views/diagnostic.php", true, false, "Diagnostic d'un serveur de jeu");