<?php
  //Définition des variables statiques pour les liens
  define('WEBROOT', str_replace('index.php','', $_SERVER['SCRIPT_NAME']));
  define('ROOT', str_replace('index.php','', $_SERVER['SCRIPT_FILENAME']));

  //On démarre les sessions pour avoir les variables superglobal sur le joueur.
  session_start();

  /* ====DEBUG==== */
  //echo ROOT;
  //echo WEBROOT;
  //echo $_SERVER['HTTP_HOST'];
  /* ============= */

  //On charge ce fichier pour pouvoir utiliser la variable(tableau) $Serveur_Config qui récupère le contenu des fichiers config du site.
  require(ROOT.'controllers/config_server.php');

  //Récupération du fichier source
  require_once(ROOT.'controllers/controleur.php');
  $controleur_def = new Controleur($Serveur_Config);

  //require(ROOT . "views/themes/" . $Serveur_Config['theme'] . "/CSS/statics.php");

  //Si le site n'est pas installé, on charge le dossier installation
  if ($Serveur_Config['is_install'] != true){
    header('Location: '. ROOT .'/installation/');
  }

  //Réqupération du Get p pour la redirection des pages
  if(isset($_GET['p'])){
      $param = explode('/',$_GET['p']);
  }

  if ($param[0] == "uploads"){
    require ('controllers/img.php');
    die();
  }

  //On récupère la page demandée
  if (isset($param[0])) {
    if (!isset($param[1])){
        //Si la page exsiste on appelle le controlleur, on en profite pour mettre en minuscule le param[0] avec la fonction mb_strtolower qui agit
        //aussi sur les caractère Polonais et spéciaux (contrairement à strtolower).
        if (is_file(ROOT . 'controllers/' . mb_strtolower($param[0], 'UTF-8') . '.php')){
          require(ROOT . 'controllers/'. $param[0] .'.php');
          //si l'URL ne contient rien, alors on charge l'accueil
        }else if ($param[0] == null){
          //$controleur_def->loadView('accueil', 'accueil');
          require(ROOT . 'controllers/accueil.php');
          //Sinon on charge le controleur de l'erreur 404
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
      }else {
        echo 'Erreur redirect 1';
      }
    }
  }
