<?php
//Si on passe en mode action (XHR) -------------------------------------------------
//On verifie que l'url correspond bien à une action
if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2])){
  //On vérifie que l'utilisateur peut effectuer cette action
  if (isset($_SESSION['user']) && !empty($_SESSION['user']) 
  && $_SESSION['user']->getLevel() >= 3 
  && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(), $infos[0]['role']))
  ){
    //On charge le model
    $controleur_def->loadModel('comptes/comptes');
    if ($param[1] == "ban"){
      if (isset($_POST['reason'])){
        ban($controleur_def->bddConnexion(), $param[2], $_POST['reason']);
        die("Success");
      }else {
        ban($controleur_def->bddConnexion(), $param[2]);
        die("Success");
      }
    }else if ($param[1] == "supp"){
      supp($controleur_def->bddConnexion(), $param[2]);
      die("Success");
    }
  }else {
    die("Niveau d'autorisation non suffisant");
  }
  //Si on passe en mode deconnexion ------------------------------------------------
}else if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && isset($param[1]) && !empty($param[1]) && $param[1] == "deconnexion"){
  //On charge le model
  $controleur_def->loadModel('comptes/comptes');
  //On appel la fonction
  disconnect();
  //On le redirige
  header('Location: '. $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);   
  
  //Si on passe en mode affichage profil d'un membre -------------------------------
  //On verifie qu'il ya bien un pseudo dans l'url
}else if (isset($param[1]) && !empty($param[1])){
  //On va charger le compte du joueur préciser dans l'url
  //On charge le model
  $controleur_def->loadModel('comptes/comptes');
  //On récupère les infos :
  $infos = getInfo($controleur_def->bddconnexion(), $param[1]);
    
  //On créé la variable contenant le pseudo pour le passer à la vue
  $pseudo = $param[1];
  $not_user = true;

  //On vérifie si l'utilisateur peut ban ce compte
  if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() >= 3 && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(), $infos[0]['role']))){
    $can_ban = true;
  }else {
    $can_ban = false;
  }

  //Ensuite on récupère ses dernières actions
  $lastactions = getLastActions($controleur_def->bddconnexion(), $param[1], 0, 10);
  foreach ($lastactions as $key => $lastaction) {
    $lastactions[$key]['id_post'] = getPost($controleur_def->bddconnexion(),$lastactions[$key]['id_post']);
  }

  //On charge la vue
  $controleur_def->loadView('pages/compte', 'emptyServer', 'Mon Compte');

  //On arrete l'execution du script
  die();
//Si on passe en mode affichage du compte de l'utilisateur en session------------------
}else if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){
  //On charge le model
  $controleur_def->loadModel('comptes/comptes');

  //On créé la variable contenant le pseudo pour le passer à la vue
  $pseudo = $_SESSION['pseudo'];
  $not_user = false;

  $infos = getInfo($controleur_def->bddconnexion(), $_SESSION['pseudo']);

  if (empty($infos) && !empty($_SESSION['pseudo'])){
    disconnect();
    header('Location: '. $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);
  }
  $lastactions = getLastActions($controleur_def->bddconnexion(), $_SESSION['pseudo'], 0, 10);
  foreach ($lastactions as $key => $lastaction) {
    $lastactions[$key]['id_post'] = getPost($controleur_def->bddconnexion(),$lastactions[$key]['id_post']);
  }
  //On charge la vue
  $controleur_def->loadView('pages/compte', 'emptyServer', 'Mon Compte');
}