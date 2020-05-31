<?php
$not_found = false;
//Si on reçoit des données $_POST (modification du profil par le membre)
//Avant de vérifier $_POST, on vérifie l'identité de l'utilisateur
if (isset($_SESSION['user']) &&
    isset($_POST['pseudo']) && !empty($_POST['pseudo']) && isset($_POST['email']) && !empty($_POST['email'])){
    //Ensuite, on vérifie si vraiment les informations ont changées (sauf si il y a une image, auquel cas on update toutes les informations)
    if (isset($_FILES['img']) && $_FILES['img']['size'] != 0) {
      if ($_FILES['img']['type'] == "image/png" || $_FILES['img']['type'] == "image/jpeg") {
        $upload = uploadFile('img', "profiles");
        if (is_int($upload)){
            $controleur_def->addError(500 + intval($upload));
        }else {
          $filename = $upload;
          $_SESSION['user']->reload($controleur_def->bddConnexion());
          $img_old = $_SESSION['user']->getInfo()['profile_img'];
            if (simplifySQL\update(
                      $controleur_def->bddConnexion(), 
                      "d_membre", 
                      array( 
                        array( 'email', "=", htmlspecialchars( $_POST['email'] ) ), 
                        array( 'pseudo', "=", htmlspecialchars($_POST['pseudo']) ),
                        array( 'profile_img', "=", htmlspecialchars($filename) ),
                      ), 
                      array( array("id", "=", $_SESSION['user']->getId() ) )
                      ) != false){
              if ($img_old != "profiles/no_profile.png"){
                if (@unlink(ROOT . 'views/uploads/img/' . $img) == false){
                    $controleur_def->addError(540);
                }
              }

              $_SESSION['user']->reload($controleur_def->bddConnexion());

            }else {
              $controleur_def->addError("342a");
            }
        }
      }else {
        $controleur_def->addError(524);
      }
    }else if ($_POST['pseudo'] != $_SESSION['user']->getPseudo() || $_POST['email'] != $_SESSION['user']->getInfo()['email']){
        if (simplifySQL\update(
                    $controleur_def->bddConnexion(), 
                    "d_membre", 
                    array( 
                      array( 'email', "=", htmlspecialchars( $_POST['email'] ) ), 
                      array( 'pseudo', "=", htmlspecialchars($_POST['pseudo']) )
                    ), 
                    array( array("id", "=", $_SESSION['user']->getId() ) )
                    ) != false){
         $_SESSION['user']->reload($controleur_def->bddConnexion());
       }else {
         $controleur_def->addError("342a");
       }
    }
}
//Si on passe en mode action (XHR) -------------------------------------------------
//On verifie que l'url correspond bien à une action
if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2])){
  //On vérifie que l'utilisateur peut effectuer cette action
  if (isset($_SESSION['user']) && !empty($_SESSION['user']) 
  && $_SESSION['user']->getLevel() >= 4 
  && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevelByPseudo($param[2]))
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
      //On récupère l'id du membre
      $id = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "id", array(array("pseudo", "=", $param[2])));
      if (!empty($id)){
        simplifySQL\delete($controleur_def->bddConnexion(), "d_forum_com", array(array("user", "=", $id['id'])));
        simplifySQL\delete($controleur_def->bddConnexion(), "d_forum", array(array("user", "=", $id['id'])));
      }
      
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
  
  if (empty($infos)){
    $not_found = true;
  }
  //On créé la variable contenant le pseudo pour le passer à la vue
  $pseudo = $param[1];
  $not_user = true;

  //Si on a bien trouvé un membre, on charge les informations, sinon on s'arrête, la vue chargera un msg d'erreur
  if ($not_found == false){
    //On récupère le grade du membre
    $g = simplifySQL\select($controleur_def->bddConnexion(), true, "d_roles", "*", array(array("id", "=", $infos[0]['role']))); 
    if (!empty($g)){
      $infos[0]['grade'] = $g['name'];
    }else {
      $not_found = true;
    }
  }

  //Si on a bien trouvé un membre, on charge les informations, sinon on s'arrête, la vue chargera un msg d'erreur
  if ($not_found == false){
    //On vérifie si l'utilisateur peut ban ce compte
    if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() >= 4 && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(), $infos[0]['role']))){
      $can_ban = true;
    }else {
      $can_ban = false;
    }

    //Ensuite on récupère ses dernières actions
    $lastactions = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_com", array("id", "content_com", "user", "id_post", array("date_comment", "%d/%m/%Y\ à %Hh:%imin", "date_com")), array(array("user", "=", $infos[0]['id'])), "date_comment", true, array(0, 10));
    //var_dump($lastactions);die;
    foreach ($lastactions as $key => $lastaction) {
      $lastactions[$key]['id_post'] = getPost($controleur_def->bddconnexion(),$lastactions[$key]['id_post']);
      //On convertit les id des comptes en pseudos
      $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo, profile_img', array(array("id", "=", $lastactions[$key]['id_post']['user'])));
      $ps = $membre['pseudo'];
      if (empty($pseudo)){
        $lastactions[$key]['id_post']['user'] = "Utilisateur inconnu";
      }else {
        $lastactions[$key]['id_post']['user'] = $ps;
      }
    }
  }

  //On charge la vue
  $controleur_def->loadJS('comptes');
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
  $g = simplifySQL\select($controleur_def->bddConnexion(), true, "d_roles", "*", array(array("id", "=", $infos[0]['role']))); 
  if (!empty($g)){
    $infos[0]['grade'] = $g['name'];
  }else {
    disconnect();
    header('Location: '. $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);
  }

  $lastactions = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_com", array("id", "content_com", "user", "id_post", array("date_comment", "%d/%m/%Y\ à %Hh:%imin", "date_com")), array(array("user", "=", $_SESSION['user']->getId())), "date_comment", true, array(0, 10));  
  foreach ($lastactions as $key => $lastaction) {
    $lastactions[$key]['id_post'] = getPost($controleur_def->bddconnexion(),$lastactions[$key]['id_post']);
    $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo', array(array("id", "=", $lastactions[$key]['id_post']['user'])));
    $ps = $membre['pseudo'];
    if (empty($pseudo) || $membre == false){
      $lastactions[$key]['id_post']['user'] = "Utilisateur inconnu";
    }else {
      $lastactions[$key]['id_post']['user'] = $ps;
    }
  }

  $commandes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*", array(array("id_user", "=", $_SESSION['user']->getid())), "id", true);
  foreach ($commandes as $k => $c){
    $commandes[$k]['article'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "*", array(array("id", "=", $commandes[$k]['id_article'])));
  }

  //On charge la vue
  $controleur_def->loadJS('comptes');
  $controleur_def->loadView('pages/compte', 'emptyServer', 'Mon Compte');
}