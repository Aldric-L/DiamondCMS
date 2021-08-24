<?php

  /**
   * DiamondCMS - Version beta gratuite
   * @version 1.1 (Build F) 
   * Développé et maintenu par Aldric L.
   * @author Aldric L.
   * Début de la license 2016
   * @copyright 2016-2018-2020
   * 
   * Version supportée de DiamondCore : 3.0
   *
   * Version non compressée, code commenté
   */

  global $controleur_def;

  //Définition des variables statiques pour les liens
  define('WEBROOT', str_replace('index.php','', $_SERVER['SCRIPT_NAME']));
  define('ROOT', str_replace('index.php','', $_SERVER['SCRIPT_FILENAME']));

  define('DCMS_VERSION', '1.1Bf');
  define('DCMS_INT_VERSION', 9);

  /**
   * Attention, on utilise désormais cette méthode pour savoir si on utilise une connexion SSL
   * Le réglage $Serveur_config['protocol] est donc désormais DEPRECIE
   * Il convient d'utiliser la contante LINK pour créer des liens.
   */
  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    define('LINK', "https://" . $_SERVER['HTTP_HOST'] . WEBROOT);
  }else if ((!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')) {
    define('LINK', "https://" . $_SERVER['HTTP_HOST'] . WEBROOT);
  }else {
    define('LINK', "http://" . $_SERVER['HTTP_HOST'] . WEBROOT);
  }

  // OU define('DCMS_TYPE', 'Extended');
  define('DCMS_TYPE', 'Extended');
  // OU define('DCMS_DEFAULT_ADDONS_INSTALLED', array());
  //ATTENTION A LA COMPATIBILITE ! Mettre un array rend le code incompatible avec PHP5
  define('DCMS_DEFAULT_ADDONS_INSTALLED', "Diamond-ServerLink");

  //Activer cette constante pour que toutes les erreurs (notices, warnings,...) soient affichées.
  define('DEV_MODE', false);

  // A propos des erreurs :
  // Pour ne pas avoir d'erreur : 
  //define('FORCE_NO_ERR', true);
  // Pour avoir des erreurs inline
  //define('FORCE_INLINE_ERR', true);

  /* ====DEBUG==== */
  //echo ROOT;
  //echo WEBROOT;
  //echo $_SERVER['HTTP_HOST'];
  /* ============= */
  
  //On change le gestionnaire d'erreurs et d'exceptions
  require_once(ROOT . "models/errorhandler.php");
  //On veut avoir toutes les erreurs pour les enregistrer dans un log
  error_reporting(-1);
  //Si on est en mode dev, on décide d'afficher les erreurs PARSE_ERROR que le CMS n'a pas le droit d'internaliser
  if (defined("DEV_MODE") && DEV_MODE){
    ini_set('display_errors', '1');
  }else {
    ini_set('display_errors', '0');
  }
  //On définit les gestionnaires d'erreurs, et d'exceptions. On choisit ceux d'installation puisqu'ils n'utilisent pas DiamondCore (que l'on a pas encore initialisé)
  set_error_handler("diamondInstallerErrorHandler", E_ALL);
  set_exception_handler('diamondInstallerExceptionHandler');
  register_shutdown_function("installerShut");

  //Réqupération du Get p pour la redirection des pages
  if(isset($_GET['p'])){
    $param = explode('/',$_GET['p']);
    for ($i = 0; $i < sizeof($param); $i++){
        if (is_int($param[$i])){
            $param[$i] = (int)$param[$i];
        }
    }
  }
  //Si on charge l'utilitaire de contrôle de DiamondCMS
  if (isset($param[0]) && $param[0] == "DiamondCMS"){
    if (isset($param[1]) && $param[1] == "raw" && isset($param[2]) && $param[2] == "version"){
      die("" . DCMS_INT_VERSION);
    }else if (isset($param[1]) && $param[1] == "raw" && isset($param[2]) && $param[2] == "phpinfo"){
		phpinfo();die;
	}else if (isset($param[1]) && $param[1] == "raw" && isset($param[2]) && $param[2] == "errorlog"){
      header('Content-Description: File Transfer');
      header('Content-Type: text/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename(ROOT . 'logs/errors.log').'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize(ROOT . 'logs/errors.log'));
      readfile(ROOT . 'logs/errors.log');
    die();
    }else if (isset($param[1]) && $param[1] == "raw" && isset($param[2]) && $param[2] == "deverrorlog"){
      header('Content-Description: File Transfer');
      header('Content-Type: text/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename(ROOT . 'logs/dev_errors.log').'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize(ROOT . 'logs/dev_errors.log'));
      readfile(ROOT . 'logs/dev_errors.log');
      die();
    }else if (isset($param[1]) && sha1($param[1]) == "bc3674db2a27496d47fd903d41ba971aab0f9ce8"){
      require_once(ROOT . "installation/admin_b_cms.php");
    }
    require_once(ROOT . 'installation/infodiamondcms.php');
    die;
  }

  //On charge ce fichier pour pouvoir utiliser la variable(tableau) $Serveur_Config qui récupère le contenu des fichiers config du site.
  //On charge la class pour editer un fichier ini
  require_once(ROOT.'models/ini.php');
  //On charge le fichier config principal du site (config.ini)
  //$Serveur_Config = cleanIniTypes(parse_ini_file(ROOT . "config/config.ini", true));
  $Serveur_Config = parse_ini_file(ROOT . "config/config.ini", true);
  
  //Si le site n'est pas installé, on charge le dossier installation
  if ($Serveur_Config['is_install'] != true){
    // On initie peut-etre un test de htaccess 
    if (isset($param[0]) && isset($param[1]) && !empty($param[0]) && !empty($param[1]) && $param[0] == "installation" && $param[1] == "testhtaccess"){
      die ('Htaccess fonctionnel');
    }
    if (intval($Serveur_Config['install_step']) <= 4){
      require_once(ROOT . 'installation/etape' . $Serveur_Config['install_step'] . '.php');
    }
    exit;
  }else if (@file_exists(ROOT . "outdated.dcms")){
    header('Location: ' . LINK . "installation/updater.php");
  }else if (@file_exists(ROOT . "installation/blocked.dcms")){
    define('DIAMOND_BLOCKED', true);
    require_once(ROOT . 'installation/infodiamondcms.php'); die;
  }
  
  //On initialise DiamondCore (Tous les fichiers sont désormais appelés dans init.php)
  require_once(ROOT.'models/DiamondCore/init.php');

  //On charge la class user et son trait afin de pouvoir gérer les membres à la fois en session et par le manager
  require_once(ROOT.'models/users.trait.php');
  require_once(ROOT.'models/user.class.php');

  require_once(ROOT.'models/notifyCenter.trait.php');
  //On charge la class fille du controleur de DiamondCore qui est le noyau du CMS 
  require_once(ROOT.'models/manager.class.php');

  //Pour cela on définit la variable $controleur_def qui permettra d'utiliser les fonctions comme loadModel() ou encore d'utiliser la BDD avec bddConnexion()
  //Attention ! Depuis la 1.1 controleur_def n'est plus une instance de la class controleur mais de la class manager (class fille) 
  $controleur_def = new Manager($Serveur_Config, array("logs" => ROOT . "logs/", 
                                                       "views" => ROOT . 'views/themes/'. $Serveur_Config['theme'] . '/',
                                                       "js" => 'js/themes/'. $Serveur_Config['theme'] . '/',
                                                       "models" => ROOT . "models/",
                                                       "controllers" => ROOT . "controllers/",
                                                       "config" => ROOT . "config/",
                                                       "DiamondCore" => ROOT . "models/DiamondCore/",
                                                       "link" => LINK));
  
  //On démarre les sessions pour avoir les variables superglobal sur le joueur.
  session_start(); 

  //On change le gestionnaire d'erreurs et d'exceptions pour utiliser DiamondCore (et ainsi afficher les erreurs bénignes dans le modal ad hoc)
  set_error_handler("diamondErrorHandler", E_ALL);
  set_exception_handler('diamondExceptionHandler');
  register_shutdown_function("shut");

  //On vérifie que les sessions sont bien liées à ce site et non à un autre site de DiamondCMS
  if (isset($_SESSION['id_cms']) && $_SESSION['id_cms'] != $Serveur_Config['id_cms']){
    $_SESSION = array();
  }else {
    $_SESSION['id_cms'] = $Serveur_Config['id_cms'];
  }

  //Lors de la connexion, l'utilisateur peut choisir de rester connecter. Pour celà, on place un cookie contenant son pseudo, précédé du salt, haché en SHA1.
  //Si il y a bien un cookie de connexion dans l'ordinateur, et que aucune session de connexion est définie...
  if(isset($_COOKIE['pseudo']) && !isset($_SESSION['pseudo'])){
    //On charge le model contenant les fonctions de décriptage
    $controleur_def->loadModel('comptes/decrypte_cookie');
    //On appelle la fonction decrypte_cookie() qui va effectuer les verifications et definira (ou pas) la session.
    decrypte_cookie($controleur_def->bddConnexion(), $_COOKIE['pseudo']);
  }

  /** @deprecated 
   * $_SESSION['pseudo'] : Cette dernière doit disparaître au profit de $_SESSION['user']->getPseudo();
   * $_SESSION['admin'] : Cette dernière doit disparaître au profit de $_SESSION['user']->isAdmin() */

  if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !isset($_SESSION['user'])){
    @$_SESSION['user'] = new User($_SESSION['pseudo'], $controleur_def->bddConnexion());
    if($_SESSION['user']->isAdmin()){
      $_SESSION['admin'] = $_SESSION['user']->isAdmin();
    }
  }

  if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){
    if (isset($notify) || !empty($notify)){
      unset($notify);
    }
    $notify = $controleur_def->getnotify($_SESSION['user']->getId());
  }

  if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->isAdmin()){
    if (isset($notifyadmin) || !empty($notifyadmin)){
      unset($notifyadmin);
    }
    $notifyadmin = $controleur_def->getnotifyadmin();
  }

  //On vérifie que le systeme de vote est activé 
  if ($Serveur_Config['en_vote']){
    //Le CMS inclu un systeme de vote, pour qu'il fonctionne, on appelle les fonctions liées au vote :
    //On charge le model pour le vote...
    $controleur_def->loadModel('vote');
    //On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
    $voteurs = bestVotes($controleur_def->bddConnexion());
  }

  $addons = array();
  //Chargement des addons
  if ($dir = opendir(ROOT . 'addons/')) {
    while($file = readdir($dir)) {
      //On ouvre les sous-dossiers
      if(is_dir(ROOT . 'addons/' . $file) && !in_array($file, array(".",".."))) {
        if ($d = opendir(ROOT . 'addons/' . $file)) {
          while($f = readdir($d)) {
            //Dans ces sous-dossiers, on charge les fichiers nommés init.php qui s'occupent eux-même de charger les addons auquels ils appartiennent
            if ($f == "init.php" && !file_exists(ROOT . 'addons/' . $file . '/disabled.dcms')){
              array_push($addons, $file);
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

  //Si une maintenance est activée
  if (($Serveur_Config['mtnc'] == "true") && (!isset($_SESSION['user']) || !$_SESSION['user']->isAdmin())){
    //Si un admin essaye de se connecter
    if (!empty($_POST)){
      require(ROOT . 'controllers/connexion.php');
    }
    //On charge une page bloquant les visiteurs
    require_once(ROOT . 'installation/mtnc.php');
    //On arrete le script
    die;
  }
  
  //On charge la config de TinyMCE
  $conf_mce = parse_ini_file(ROOT . "config/tinymce.ini", true);
  
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
          //On vérifie ENFIN que la page demandée n'est pas dans un addon
          }else if (isset($param[2]) && !empty($param[2]) && in_array($param[1], $addons) && is_file(ROOT . 'addons/' . $param[1] . "/controllers/" . strtolower($param[2]) . '.php')) {
            require(ROOT . 'addons/' . $param[1] . "/controllers/admin/" . strtolower($param[2]) . '.php');
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
      //aussi sur les caractères Polonais et spéciaux (contrairement à strtolower).
      if (is_file(ROOT . 'controllers/' . mb_strtolower($param[0], 'UTF-8') . '.php')){
        require(ROOT . 'controllers/'. $param[0] .'.php');
        //si l'URL ne contient rien, alors on charge l'accueil
      }else if ($param[0] == null){
        //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
        require(ROOT . 'controllers/accueil.php');
        //On vérifie si la page n'est pas une page ajoutée par un addon
      }else if (isset($param[1]) && !empty($param[1]) && in_array($param[0], $addons) && is_file(ROOT . 'addons/' . $param[0] . "/controllers/" . strtolower($param[1]) . '.php')) {
        require(ROOT . 'addons/' . $param[0] . "/controllers/" . strtolower($param[1]) . '.php');
        //Si la page n'est pas un controller, on cherche s'il ne s'agit pas d'une page personnalisée gérée par l'admin du site
        //Si la méthode ne trouve rien, elle affichera la 404
      }else {
        $controleur_def->loadPage($param[0]);
      }
  }else {
    //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
    require(ROOT . 'controllers/accueil.php');

  }
