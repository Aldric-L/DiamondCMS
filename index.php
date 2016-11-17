<?php
  //Définition des variables statiques pour les liens
  define('WEBROOT', str_replace('index.php','', $_SERVER['SCRIPT_NAME']));
  define('ROOT', str_replace('index.php','', $_SERVER['SCRIPT_FILENAME']));

  //On démarre les sessions pour avoir les variables superglobal sur le joueur.
  /*ini_set('session.save_handler', 'files');
  $handler = new EncryptedSessionHandler('mykey');
  session_set_save_handler($handler, true);*/
  session_start();

  /* ====DEBUG==== */
  //echo ROOT;
  //echo WEBROOT;
  //echo $_SERVER['HTTP_HOST'];
  /* ============= */

  //On charge ce fichier pour pouvoir utiliser la variable(tableau) $Serveur_Config qui récupère le contenu des fichiers config du site.
  require(ROOT.'controllers/config_server.php');

  //Récupération du fichier source "Controleur" qui sera la base de la partie contollers du CMS
  require_once(ROOT.'controllers/controleur.php');
  //Pour cela on définit la variable $controleur_def qui permettra d'utiliser les fonctions comme loadModel() ou encore d'utiliser la BDD avec bddConnexion()
  $controleur_def = new Controleur($Serveur_Config);
  $controleur_def->isValid();

  //Si le site n'est pas installé, on charge le dossier installation
  if ($Serveur_Config['is_install'] != true){
    header('Location: '. ROOT .'/installation/');
  }

  //Réqupération du Get p pour la redirection des pages
  if(isset($_GET['p'])){
      $param = explode('/',$_GET['p']);
  }

  //Lors de la connexion, l'utilisateur peut choisir de rester connecter. Pour celà, on plasse un cookie contenant son pseudo haché en SHA1.
  //Si il y a bien un cookie de connexion dans l'ordinateur, et que aucune session de connexion est définie...
  if(isset($_COOKIE['pseudo']) && !isset($_SESSION['pseudo'])){
    //On charge le model contenant les fonctions de décriptage
    $controleur_def->loadModel('comptes/decrypte_cookie');
    //On appelle la fonction decrypte_cookie() qui va effectuer les verifications et definira (ou pas) la session.
    decrypte_cookie($controleur_def->bddConnexion(), $_COOKIE['pseudo']);
  }

  //Le CMS inclus un systeme de vote, pour qu'il fonctionne, on appelle les fonctions liées au vote :
  //On charge le model pour le vote...
  $controleur_def->loadModel('vote');
  //On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
  $voteurs = bestVotes($controleur_def->bddConnexion());

  //On charge JSONAPI
  $controleur_def->loadModel('JsonAPI/jsonapi.class');
  $jsonapi = new Jsonapi_control(1);

  //On récupère la page demandée
  if (isset($param[0])) {
    if (!isset($param[1]) || $param[1] == ""){
        //Si la page exsiste on appelle le controlleur, on en profite pour mettre en minuscule le param[0] avec la fonction mb_strtolower qui agit
        //aussi sur les caractère Polonais et spéciaux (contrairement à strtolower).
        if (is_file(ROOT . 'controllers/' . mb_strtolower($param[0], 'UTF-8') . '.php')){
          require(ROOT . 'controllers/'. $param[0] .'.php');
          //si l'URL ne contient rien, alors on charge l'accueil
        }else if ($param[0] == null){
          //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
          require(ROOT . 'controllers/accueil.php');
          //Sinon on charge le controleur de l'erreur 404
          //Si la page est cgu ou cgv on charge la page CGV/cgu
        }else if ($param[0] == "cgu" || $param[0] == "cgv" || $param[0] == "CGU" || $param[0] == "CGV") {
          $controleur_def->loadView('pages/cgu', '', 'CGU / CGV');
        }else {
          //On charge la vue, la fonction va charger 3 fichiers.
          $controleur_def->loadView('pages/404', '404', 'Erreur 404');
        }
    }

    //Si il y a un deuxième paramètre dans l'url :
    if (isset($param[1])) {
      //Si ce paramètre est f_com et qu'il est associer de forum et d'un id (/forum/f_com/00) alors on charge le controlleur.
      if($param[0] == 'forum' && $param[1] == 'com' && $param[2] != null){
        require(ROOT . 'controllers/forum_com.php');
      }
    }
  }
