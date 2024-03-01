<?php
//On charge le contenu du text d'affichage sur la photo
$content_photo = file_get_contents(ROOT . "config/accueil.ftxt");
if (!(isset($_SESSION['editing_mode']) && $_SESSION['editing_mode'])){
    foreach (TEXT_ALIAS as $key => $a){
        $content_photo = str_replace($key, $a, $content_photo);
    }
}


if (defined("DServerLink") && DServerLink){
  $modules = array(array("mod_name" => "WhyAreWeBetter", "parameters" => null),
                   array("mod_name" => "ServerState", "parameters" => null),
                   array("mod_name" => "OurTeam", "parameters" => null),
                   array("mod_name" => "LatestNews", "parameters" => null));
}else {
  $modules = array(array("mod_name" => "WhyAreWeBetter", "parameters" => null),
                   array("mod_name" => "OurTeam", "parameters" => null),
                   array("mod_name" => "LatestNews", "parameters" => null));
}

try {
  $modulesmanager = $controleur_def->getModulesManager("accueil");
  $modulesmanager->init($controleur_def->bddConnexion(), $modules);
} catch (\DiamondException $e) {
  $controleur_def->addError($e->getCode());
}

$controleur_def->loadView('pages/accueil', 'accueil', 'Accueil');
