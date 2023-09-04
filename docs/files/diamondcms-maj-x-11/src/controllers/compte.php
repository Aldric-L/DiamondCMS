<?php
$controleur_def->loadModel('hydratation/userhydrate.class');

if (empty($param[1]) && !isset($_SESSION['user']))
  header('Location: ' . LINK . 'connexion/');

try {
  $user = new UserHydrate((!empty($param[1])) ? $param[1] : $_SESSION['user']->getPseudo(), $controleur_def->bddconnexion(), (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user'] : null);
  $lastactions = $user->get_lastActions($controleur_def->bddConnexion());
}catch (Exception $e){
  if ($e->getCode() == 332){
    $controleur_def->nonifyPage("Impossible de poursuivre", "Le compte demandé est introuvable.");die;
  }
}

if ($user->isBanned()){
  $controleur_def->nonifyPage("Impossible de poursuivre", "Le compte demandé est banni.");die;
}

try {
  $modulesmanager = $controleur_def->getModulesManager("compte");
  $modulesmanager->init($controleur_def->bddConnexion(), array(array("mod_name" => "UserLastActions", "parameters" => array($user))));
} catch (\DiamondException $e) {
  $controleur_def->addError($e->getCode());
}

$controleur_def->loadView('pages/compte', '', 'Mon Compte');