<?php

  /**
   * DiamondCMS - Vertion beta gratuite
   * @version 1.0 Build C
   * Développé et maintenu par Aldric L.
   * @author Aldric L.
   * Début de la license 2016
   * @copyright 2016-2018-2020
   *
   * Site officiel : diamondcms.fr
   * Version non compressée, code commenté
   */

  //Définition des variables statiques pour les liens
  define('WEBROOT', str_replace('index.php','', $_SERVER['SCRIPT_NAME']));
  define('ROOT', str_replace('index.php','', $_SERVER['SCRIPT_FILENAME']));

  require_once(ROOT.'controllers/user.php');

  //On démarre les sessions pour avoir les variables superglobal sur le joueur.
  session_start(); 
  /* ====DEBUG==== */
  //echo ROOT;
  //echo WEBROOT;
  //echo $_SERVER['HTTP_HOST'];
  /* ============= */

  //On charge ce fichier pour pouvoir utiliser la variable(tableau) $Serveur_Config qui récupère le contenu des fichiers config du site.
  //On charge le fichier config principal du site (config.ini)
  $Serveur_Config = parse_ini_file(ROOT . "config/config.ini", true);
  //On charge la class pour editer un fichier ini
  require_once(ROOT.'models/ini.php');

  //On charge le model des models pour faciliter les requettes SQL-PDO
  require_once(ROOT.'models/core.php');

  //Récupération du fichier source "Controleur" qui sera la base de la partie contollers du CMS
  //ATTENTION la class contrôleur a besoin des fonctions du modèle core et doit donc être inclu après ce premier.
  require_once(ROOT.'controllers/controleur.php');
  //Pour cela on définit la variable $controleur_def qui permettra d'utiliser les fonctions comme loadModel() ou encore d'utiliser la BDD avec bddConnexion()
  $controleur_def = new Controleur($Serveur_Config);
  
  //Lors de la connexion, l'utilisateur peut choisir de rester connecter. Pour celà, on place un cookie contenant son pseudo, précédé du salt, haché en SHA1.
  //Si il y a bien un cookie de connexion dans l'ordinateur, et que aucune session de connexion est définie...
  if(isset($_COOKIE['pseudo']) && !isset($_SESSION['pseudo'])){
    //On charge le model contenant les fonctions de décriptage
    $controleur_def->loadModel('comptes/decrypte_cookie');
    //On appelle la fonction decrypte_cookie() qui va effectuer les verifications et definira (ou pas) la session.
    decrypte_cookie($controleur_def->bddConnexion(), $_COOKIE['pseudo']);
  }

  /** @deprecated $_SESSION['pseudo'] : Cette dernière doit disparaître au profit de $_SESSION['user']->getPseudo(); */

  if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !isset($_SESSION['user'])){
    @$_SESSION['user'] = new User($_SESSION['pseudo'], $controleur_def->bddConnexion());
  }

  if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){
    if (isset($notify) || !empty($notify)){
      unset($notify);
    }
    $notify = $controleur_def->getnotify($_SESSION['pseudo']);
  }

  if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->isAdmin()){
    if (isset($notifyadmin) || !empty($notifyadmin)){
      unset($notifyadmin);
    }
    $notifyadmin = $controleur_def->getnotifyadmin();
  }
  
  //Si le site n'est pas installé, on charge le dossier installation
  if ($Serveur_Config['is_install'] != true){
    header('Location: '. ROOT .'/installation/etape1.php');
    exit;
  }

  //Réqupération du Get p pour la redirection des pages
  if(isset($_GET['p'])){
      $param = explode('/',$_GET['p']);
      //var_dump($param); die;
  }

  //On vérifie que le systeme de vote est activé 
  if ($Serveur_Config['en_vote']){
    //Le CMS inclus un systeme de vote, pour qu'il fonctionne, on appelle les fonctions liées au vote :
    //On charge le model pour le vote...
    $controleur_def->loadModel('vote');
    //On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
    $voteurs = bestVotes($controleur_def->bddConnexion());
  }

  //Chargement des addons
  if ($dir = opendir(ROOT . 'addons/')) {
    while($file = readdir($dir)) {
      //On ouvre les sous-dossiers
      if(is_dir(ROOT . 'addons/' . $file) && !in_array($file, array(".",".."))) {
        if ($d = opendir(ROOT . 'addons/' . $file)) {
          while($f = readdir($d)) {
            //Dans ces sous-dossiers, on charge les fichiers nommés init.php qui s'occupent eux-même de charger les addons auquels ils appartiennent
            if ($f == "init.php"){
              require_once(ROOT . 'addons/' . $file . '/' . $f);
            }
          }
          closedir($d);
        }
      }
    }
    closedir($dir);
  }
  if (!defined("DServerLink")){
    define("DServerLink", false);
  }
  
  //On récupère la page demandée
  if (isset($param[0])) {
      //On verifie en premier le lieu que la page de mandée ne soit pas une page d'administration
      if ($param[0] == "admin"){
        //Nous somme bien en situation d'administration
        //Avant d'appeler la page d'administration, on verifie que l'utilisateur est bien admin
        if (isset($_SESSION['user']) && $_SESSION['user']->isAdmin()){
          //l'utilisateur est bien admin
          //On appel donc les pages d'administration qui se trouvent dans le dossier admin (controllers/admin)
          if (isset($param[1]) && is_file(ROOT . 'controllers/admin/' . mb_strtolower($param[1], 'UTF-8') . '.php')){
            require(ROOT . 'controllers/admin/'. $param[1] .'.php');
            //si l'URL ne contient rien, alors on charge l'accueil
          }else if (isset($param[1]) && $param[1] == null){
            //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
            require(ROOT . 'controllers/admin/accueil.php');
          }else {
            //Sin on ne trouve pas de destionation, on charge la 404
            //On charge la vue, la fonction va charger 3 fichiers.
            $controleur_def->loadView('pages/404', '404', 'Erreur 404');
          }
          exit();
        }else {
          if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !$_SESSION['user']->isAdmin()){
            //L'utilisateur essaye d'appeler une page admin alors qu'il ne l'est pas 
            //On le redirige vers l'accueil
            header('Location: ' . $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);
          }else {
            //L'utilisateur essaye d'appeler une page admin alors qu'il n'est pas connecté
            //On le redirige vers la page de connexion
            header('Location: ' . $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'connexion');
          }
          

        }
      }
      //Si la page exsiste on appelle le controlleur, on en profite pour mettre en minuscule le param[0] avec la fonction mb_strtolower qui agit
      //aussi sur les caractère Polonais et spéciaux (contrairement à strtolower).
      if (is_file(ROOT . 'controllers/' . mb_strtolower($param[0], 'UTF-8') . '.php')){
        require(ROOT . 'controllers/'. $param[0] .'.php');
        //si l'URL ne contient rien, alors on charge l'accueil
      }else if ($param[0] == null){
        //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
        require(ROOT . 'controllers/accueil.php');
        //Si la page est cgu ou cgv on charge la page CGV/cgu
      }else if (strtolower($param[0]) == "cgu" || strtolower($param[0]) == "cgv") {
        $controleur_def->loadView('pages/cgu', '', 'CGU / CGV');
      }else if (strtolower($param[0]) == "reglement" && $Serveur_Config['en_reglement']) {
        $controleur_def->loadView('pages/reglement', '', 'Réglement du Serveur');
      }else if (strtolower($param[0]) == "mentions-legales") {
        $controleur_def->loadView('pages/m-legal', '', 'Mentions légales');
      }else {
        //Si on ne trouve pas de destionation, on charge la 404
        //On charge la vue, la fonction va charger 3 fichiers.
        $controleur_def->loadView('pages/404', '404', 'Erreur 404');
      }
  }else {
    //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
    require(ROOT . 'controllers/accueil.php');

  }
