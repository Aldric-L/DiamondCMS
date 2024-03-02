<?php

//
//   .JP5555555555555555555555555555555555555555555555P5JYPJ.  
//  .JP55555555555555555555555555555555555555555555555Y?Y55PY. 
// .J55YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY5555555Y?55555PY:
// :JYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYJ?555555JJ555555PY:        DiamondCMS - Version alpha gratuite
//  .JP555555555555555555555555555555555555JJ555555JY555555P?.         @version 2.0 (Build A bis) 01-03-2024
//    !55555555555555555555555555555555555JY555555?Y5555555!           Développé et maintenu par Aldric L.
//     ^5P555555555555555YYYYYYYYYYYYYYYY?Y55555Y?555555PY^            Début de la license 2016
//      :JP5555557JYYYYYY?:             !555555JJ555555PJ.             @copyright 2016-2018-2020-2021-2022-2023-2024
//        7P555555?YP5555PY:           !555555JY555555P7       
//         ~5P55555JJ55555P5!        .?P55555?Y55555P5~        
//          :YP55555Y?555555P?.     .JP5555Y?555555PY:                 Version supportée de DiamondCore : 4.0
//           .?P555555?Y55555PY^   :YP5555JJ555555P?.                  Version non compressée, code commenté
//             !5555555JJ5555555! ~555555JJ5555555!            
//              ^YP55555J?555555P?J55555?Y55555P5^             
//               .JP55555Y?Y55555PY?5PY?555555PJ:              
//                 75555555?J5555555JJJ555555P?.               
//                  ^5P55555J?5555555Y5555555!                 
//                   :JP55555Y?Y5555555555PY^                  
//                     7P555555?J55555555PJ.                   
//                      ~5P55555J?55555557                     
//                       :YP55555Y?555P5~                      
//                        .?55555P5?YPJ:                          
//                                                  
    

  global $controleur_def;
  
  //Définition des variables statiques pour les liens
  define('WEBROOT', str_replace('index.php','', $_SERVER['SCRIPT_NAME']));
  define('ROOT', str_replace('index.php','', $_SERVER['SCRIPT_FILENAME']));

  define('DCMS_VERSION', '2.0');
  define('DCMS_INT_VERSION', 11);

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
  define('DCMS_DEFAULT_ADDONS_INSTALLED', array("Diamond-ServerLink", "Diamond-AdvancedStatistics"));

  //Activer cette constante pour que toutes les erreurs (notices, warnings,...) soient affichées.
  define('DEV_MODE', false);

  //Activer ou non la fonction cache du CMS (à éviter sur des SSD)
  define('DIAMOND_CACHE', true);

  // A propos des erreurs :
  // Pour ne pas avoir d'erreur : 
  //define('FORCE_NO_ERR', true);
  // Pour avoir des erreurs inline
  //define('FORCE_INLINE_ERR', true);
  // Pour avoir des erreurs sous forme de DiamondException
  //define('FORCE_EXC_ERR', true);

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

  //On initialise DiamondCore (Tous les fichiers sont désormais appelés dans init.php)
  require_once(ROOT.'models/shortcuts.php');
  require_once(ROOT.'models/DiamondCore/init.php');
  require_once(ROOT.'models/DiamondException.class.php');

  if(array_key_exists('ENV_HTACCESS_ALLOWED', $_SERVER) 
  && ((is_bool($_SERVER["ENV_HTACCESS_ALLOWED"]) && !$_SERVER["ENV_HTACCESS_ALLOWED"]) OR (is_string($_SERVER["ENV_HTACCESS_ALLOWED"]) && $_SERVER["ENV_HTACCESS_ALLOWED"] == "false")))
    throw new Error("Le serveur WEB Apache est mal configuré car le module mod_rewrite n'est pas activé.", 998);

  $param = array();
  //Réqupération du Get p pour la redirection des pages
  if(isset($_GET['p'])){
    $param = explode('/',$_GET['p']);
    for ($i = 0; $i < sizeof($param); $i++){
        if (is_int($param[$i])){
            $param[$i] = (int)$param[$i];
        }
    }
    if (sizeof($param) === 1 && isset($param[0]) && $param[0] == "index.php")
      $param = array();
    else if (sizeof($param) > 1 && isset($param[0]) && $param[0] == "index.php")
      $param = array_slice($param, 1);
    if (sizeof($param) > 0 && end($param) == "")
      array_pop($param);
  }

  //Si on charge l'utilitaire de contrôle de DiamondCMS
  if (isset($param[0]) && $param[0] == "DiamondCMS"){
    if (isset($param[1]) && $param[1] == "raw" && isset($param[2]) && $param[2] == "version"){
      die("" . DCMS_INT_VERSION);
    }else if (isset($param[1]) && $param[1] == "raw" && isset($param[2]) && $param[2] == "phpinfo"){
		phpinfo();die;
	}else if (isset($param[1]) && $param[1] == "raw" && isset($param[2]) && $param[2] == "log" && isset($param[3]) && !empty($param[3]) && (file_exists(ROOT . 'logs/' . $param[3] . '.log') || file_exists(ROOT . 'logs/' . $param[3] . '.json.log'))){
      header('Content-Description: File Transfer');
      header('Content-Type: text/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename(ROOT . 'logs/' . $param[3] . '.log').'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      if (file_exists(ROOT . 'logs/' . $param[3] . '.log')){
        header('Content-Length: ' . filesize(ROOT . 'logs/' . $param[3] . '.log'));
        readfile(ROOT . 'logs/' . $param[3] . '.log');
      }else if(file_exists(ROOT . 'logs/' . $param[3] . '.json.log')) {
        header('Content-Length: ' . filesize(ROOT . 'logs/' . $param[3] . '.json.log'));
        readfile(ROOT . 'logs/' . $param[3] . '.json.log');
      }
    die();
    }else if (isset($_POST['bck'])){
      require_once(ROOT . "installation/admin_b_cms.php");
    }else if (@file_exists(ROOT . "installation/bck.dcms")){
      require_once(ROOT . 'installation/admin_b_cms.php'); die;
    }        
    require_once(ROOT . 'installation/infodiamondcms.php');
    die;
  }else if (@file_exists(ROOT . "installation/bck.dcms")){
    require_once(ROOT . 'installation/admin_b_cms.php'); die;
  }else if (isset($param[0]) && ($param[0] == "ext" OR $param[0] == "installation") && file_exists(ROOT . $path = $_GET["p"])){
    $extension = pathinfo(ROOT . $path);
    $extension = array_key_exists("extension", $extension) ? $extension['extension'] : "";
    if ($extension == "php"){
      require (ROOT . $path); die;
    }

    header('Content-type:'. $extension . '; charset=utf-8');
    header('Cache-control:public, max-age=604800');
    header('Accept-ranges:bytes');
    header('Content-length:'.filesize($path));
    header('Last-Modified: '.date(DATE_RFC2822, filemtime(ROOT . $path)));
    header_remove('pragma');
    die (file_get_contents(ROOT . $path));
  }else if (
  (
    isset($param) && is_array($param) && !empty($param) && (sizeof($param) > 4 && $param[0] == "views" && $param[1] == "themes" && ($param[3] == "css" OR $param[3] == "js" OR $param[3] == "CSS" OR $param[3] == "JS")) 
    OR (isset($param[0]) && ($param[0] == "js" OR $param[0] == "JS"))
    OR (sizeof($param) > 4 && $param[0] == "views" && $param[1] == "themes" && $param[3] == "src")
    OR (sizeof($param) == 4 && $param[0] == "views" && $param[1] == "themes")
    OR (sizeof($param) > 2 && $param[0] == "addons" && $param[2] == "views")
    OR (sizeof($param) == 3 && $param[0] == "addons")
  ) && file_exists(ROOT . $path = $_GET["p"])){

    $ext_to_mime = array('txt' => 'text/plain', 'md' => 'text/plain', 'htm' => 'text/html', 'html' => 'text/html', 'css' => 'text/css', 'js' => 'text/javascript', 'json' => 'application/json', 'xml' => 'application/xml', 'swf' => 'application/x-shockwave-flash',
    'woff' => 'font/woff', 'woff2' => 'font/woff2', "otf" => "application/x-font-opentype", "ttf" => "application/x-font-truetype", "eot" => "application/vnd.ms-fontobject", "svg" => "image/svg+xml", "map" => "application/json",
    "gif" => "image/gif", "png" => "image/png", "jpeg" => "image/jpeg", "jpg" => "image/jpeg", "bmp" => "image/bmp", "webp" => "image/webp", "webm" => "video/webm", "ogg" => "video/ogg", "avi" => "video/x-msvideo", "aac" => "audio/aac", "ico" => "image/x-icon",
    "midi" => "audio/midi", "mpeg" => "audio/mpeg", "weba" => "audio/webm", "ogg" => "audio/ogg", "wav" => "audio/wav", "pdf" => "application/pdf", "zip" => "application/zip", "tar" => "application/x-tar",
    "ppt" => "application/vnd.mspowerpoint", "pptx" => "application/vnd.mspowerpoint", "doc" => "application/msword", "docx" => "application/msword", "xhtml" => "application/xhtml+xml", "xml" => "application/xml",
    "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "xls"=> "application/vnd.ms-excel", "odp" => "application/vnd.oasis.opendocument.presentation", "odt" => "application/vnd.oasis.opendocument.text", "ods" => "application/vnd.oasis.opendocument.spreadsheet");
    
    $extension = pathinfo(ROOT . $path);
    $extension = array_key_exists("extension", $extension) ? $extension['extension'] : "";
    if (array_key_exists(mb_strtolower($extension), $ext_to_mime)){
      header('Content-type:'. $ext_to_mime[mb_strtolower($extension)] . '; charset=utf-8');
      header('Cache-control:public, max-age=604800');
      header('Accept-ranges:bytes');
      header('Content-length:'.filesize($path));
      header('Last-Modified: '.date(DATE_RFC2822, filemtime(ROOT . $path)));
      header_remove('pragma');
      die (file_get_contents(ROOT . $path));
    }
    
  }    

  //On charge ce fichier pour pouvoir utiliser la variable(tableau) $Serveur_Config qui récupère le contenu des fichiers config du site
  //On charge le fichier config principal du site (config.ini)
  global $Serveur_Config;
  $Serveur_Config = cleanIniTypes(parse_ini_file(ROOT . "config/config.ini", true));

  // On vérifie que le site n'a pas changé d'adresse, si c'est le cas on purge le cache par sécurité
  if (isset($Serveur_Config['is_install']) && $Serveur_Config['is_install'] == true && isset($Serveur_Config['last_url']) && $Serveur_Config['last_url'] != "" && $Serveur_Config['last_url'] != null){
    if ($Serveur_Config['last_url'] != LINK){
      if (file_exists(ROOT . 'tmp/') && $dir = opendir(ROOT . 'tmp/')) {
        while($file = readdir($dir)) {
          if(is_dir(ROOT . 'tmp/' . $file) && !in_array($file, array(".","..", "img"))) {
            rrmdir(ROOT . 'tmp/' . $file);
          }
        }
        closedir($dir);
      }
      if (!empty($SConfig = cleanIniTypes(parse_ini_file(ROOT . "config/config.ini", true)))){
        $SConfig["last_url"] = LINK;
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS'); $ini->ajouter_array($SConfig); $ini->ecrire(true);
      }
    }
  }else if (isset($Serveur_Config['is_install']) && $Serveur_Config['is_install'] == true) {
    if (!empty($SConfig = cleanIniTypes(parse_ini_file(ROOT . "config/config.ini", true)))){
      // On fait un array_merge pour insérer en haut du tableau à cause d'un bug sur certains systèmes de la classe INI
      $SConfig = array_merge(["last_url" => LINK], $SConfig);
      $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS'); $ini->ajouter_array($SConfig); $ini->ecrire(true);
    }
  }

  //Si le site n'est pas installé, on charge le dossier installation
  if (!isset($Serveur_Config['is_install']) OR $Serveur_Config['is_install'] != true){
    // On initie peut-etre un test de htaccess 
    if (isset($param[0]) && isset($param[1]) && !empty($param[0]) && !empty($param[1]) && $param[0] == "installation" && $param[1] == "testhtaccess"){
      die ('Htaccess fonctionnel');
    }
    if (isset($Serveur_Config['install_step']) && !empty($Serveur_Config['install_step']) && intval($Serveur_Config['install_step']) <= 4 && intval($Serveur_Config['install_step']) > 0){
      require_once(ROOT . 'installation/etape' . $Serveur_Config['install_step'] . '.php'); 
    }else {
        require_once(ROOT . 'installation/etape0.php'); 
    }
    exit;
  }else if (@file_exists(ROOT . "outdated.dcms")){
    header('Location: ' . LINK . "installation/updater.php");
  }else if (@file_exists(ROOT . "installation/bck.dcms")){
    require_once(ROOT . 'installation/admin_b_cms.php'); die;
  }

  require_once(ROOT.'models/user.class.php');

  require_once(ROOT.'models/notifyCenter.trait.php');
  //On charge la class fille du controleur de DiamondCore qui est le noyau du CMS 
  require_once(ROOT.'models/manager.class.php');

  //Pour cela on définit la variable $controleur_def qui permettra d'utiliser les fonctions comme loadModel() ou encore d'utiliser la BDD avec bddConnexion()
  //Attention ! Depuis la 1.1 controleur_def n'est plus une instance de la class controleur mais de la class manager (class fille) 
  $controleur_def = new Manager($Serveur_Config, array("logs" => ROOT . "logs/", 
                                                       "views" => ROOT . 'views/themes/'. $Serveur_Config['theme'] . '/',
                                                       "global_views" => ROOT . 'views/',
                                                       "js" => 'views/themes/'. $Serveur_Config['theme'] . '/js/pages/',
                                                       "models" => ROOT . "models/",
                                                       "controllers" => ROOT . "controllers/",
                                                       "config" => ROOT . "config/",
                                                       "DiamondCore" => ROOT . "models/DiamondCore/",
                                                       "cache" => ROOT . "tmp/",
                                                       "addons" => ROOT . "addons/",
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

  /** @deprecated 
   * $_SESSION['pseudo'] : Cette dernière doit disparaître au profit de $_SESSION['user']->getPseudo();
   * $_SESSION['admin'] : Cette dernière doit disparaître au profit de $_SESSION['user']->isAdmin() */

  if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && !isset($_SESSION['user'])){
    try {
      $_SESSION['user'] = new User($_SESSION['pseudo'], $controleur_def->bddConnexion());
      if($_SESSION['user']->isAdmin()){
        $_SESSION['admin'] = $_SESSION['user']->isAdmin();
      }
    } catch (\Throwable $th) {    }
  }else if(isset($_SESSION['user'])){
    try {
      $_SESSION['user']->reload($controleur_def->bddConnexion());
    }catch(Exception $e){
      User::disconnect($_SESSION['user']);
      if ($e instanceof DiamondException)
        $controleur_def->addError($e->getCode());
      else if(defined("DEV_MODE") && DEV_MODE)
        throw $e;
    }
  }

  define("TEXT_ALIAS", array(
    "{SERVER_NAME}" => $Serveur_Config['Serveur_name'], 
    "{SERVER_MONEY}" => $Serveur_Config['Serveur_money'],
    "{SERVER_DESC}" => (isset($Serveur_Config['desc']) ? $Serveur_Config['desc'] : "Un serveur de jeu exclusif avec DiamondCMS !"),
    "{LINK}" => LINK,
  ));

  //Lors de la connexion, l'utilisateur peut choisir de rester connecter. Pour celà, on place un cookie contenant son pseudo, précédé du salt, haché en SHA1.
  //Si il y a bien un cookie de connexion dans l'ordinateur, et que aucune session de connexion est définie...
  if(isset($_COOKIE['pseudo']) && !isset($_SESSION['pseudo'])){
    //On charge le model contenant les fonctions de décriptage
    $controleur_def->loadModel('comptes/decrypte_cookie');
    //On appelle la fonction decrypte_cookie() qui va effectuer les verifications et definira (ou pas) la session.
    decrypte_cookie($controleur_def->bddConnexion(), $_COOKIE['pseudo']);
  }

  if (isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && isset($_SESSION['user']) && $_SESSION['user'] instanceof User){
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
    //$controleur_def->loadModel('vote');
    //On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
    $voteurs = simplifySQL\select($controleur_def->bddConnexion(), false, "d_membre", "pseudo, votes", array("votes", ">=", 1), "votes", true, array(0,3));
  }

  require_once(ROOT.'models/pageBuilders/modules/module.class.php');
  require_once(ROOT.'models/pageBuilders/modules/modulesmanager.class.php');
  
  require_once(ROOT . 'views/themes/'. $Serveur_Config['theme'] . '/src/ModulesManager.class.php');

  //On vérifie qu'on a pas installé une mise à jour qui aurait supprimé la config du thème
  $controleur_def->loadModel('api.class');
  $controleur_def->loadModel('API/theme.class');
  try {
    if (isset($_SESSION['user']))
      $themeAPI = new theme($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, $_SESSION['user']->getLevel());
    else 
      $themeAPI = new theme($controleur_def->getPaths(), $controleur_def->bddConnexion(), $controleur_def, -1);
    $themeconf = $themeAPI->internalget_themeConf();
    if (isset($themeconf["modules_to_register"])){
      if ($dir = opendir(ROOT . 'views/themes/'. $Serveur_Config['theme'] . '/' . $themeconf["modules_to_register"])) {
        $pth = ROOT . 'views/themes/'. $Serveur_Config['theme'] . '/' . $themeconf["modules_to_register"];
        while($file = readdir($dir)) {
          if(is_dir($pth . $file) && !in_array($file, array(".",".."))) {
            $d_list = scandir($pth . $file);
              if (in_array($file . ".module.class.php", $d_list)){
                require_once($pth . $file . '/' . $file . ".module.class.php");
                $controleur_def->addAvailableModules($file, $pth . $file . '/');
              }
          }
        }
        closedir($dir);
      }
    }
  }catch (Exception $e){
    $controleur_def->log($e->getCode());
  }

  $controleur_def->loadAddons();
  $addons = $controleur_def->getAvailableAddons();

  //Si une maintenance est activée
  if (($Serveur_Config['mtnc'] == true) && (!isset($_SESSION['user']) || !$_SESSION['user']->isAdmin())){
    //Si un admin essaye de se connecter
    if (!empty($_POST)){
      require(ROOT . 'controllers/connexion.php');
    }

    if (!empty($param[0]))
      header("Location: " . LINK);

    //On charge une page bloquant les visiteurs
    require_once(ROOT . 'installation/mtnc.php');
    //On arrete le script
    die;
  }
  
  //On charge la config de TinyMCE
  $conf_mce = cleanIniTypes(parse_ini_file(ROOT . "config/tinymce.ini", true));

  if(isset($_SESSION['editing_mode']) && $_SESSION['editing_mode'])
    $controleur_def->loadJS("editing-mode");

  ob_start();

  //On récupère la page demandée
  if (isset($param[0])) {
      //On verifie en premier le lieu que la page de mandée ne soit pas une page d'administration
      if ($param[0] == "admin"){
        // Comme pour l'instant le projet PageBuilder ne concerne que l'admin, on ne le charge pas dans les autres pages
        //On charge le pagebuilder abstract
        require_once(ROOT . "models/pageBuilders/AdminBuilder/init.php");
        //On charge celui du thème
        require_once(ROOT . 'views/themes/'. $Serveur_Config['theme'] . "/src/AdminBuilder/init.php");


        //Nous sommes bien en situation d'administration
        //Avant d'appeler la page d'administration, on verifie que l'utilisateur est bien admin
        if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User && $_SESSION['user']->isAdmin()){
          //l'utilisateur est bien admin
          //On appel donc les pages d'administration qui se trouvent dans le dossier admin (controllers/admin)
          if (isset($param[1]) && is_file(ROOT . 'controllers/admin/' . mb_strtolower($param[1], 'UTF-8') . '.php')){
            require(ROOT . 'controllers/admin/'. $param[1] .'.php');
            //si l'URL ne contient rien, alors on charge l'accueil
          }else if ((isset($param[1]) && $param[1] == null) OR !isset($param[1])){
            //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
            require(ROOT . 'controllers/admin/accueil.php');
          //On vérifie ENFIN que la page demandée n'est pas dans un addon
          }else if (isset($param[2]) && !empty($param[2]) && in_array($param[1], $addons) && is_file(ROOT . 'addons/' . $param[1] . '/' . 'controllers/admin/' . strtolower($param[2]) . '.php')) {
            require(ROOT . 'addons/' . $param[1] . "/controllers/admin/" . strtolower($param[2]) . '.php');
          }else {
            //Si on ne trouve pas de destination, on charge la 404
            $controleur_def->nonifyPage("Impossible de poursuivre", "Erreur 404 : La page demandée n'existe pas");
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
        //si l'URL ne contient rien, alors on charge l'accueil ou on charge le modal de récupération de mot de passe
      }else if ($param[0] == null OR $param[0] == "endReinit" OR $param[0] == "unsubscribe"){
        //Si il n'y a pas de paramètres dans l'url, on charge l'accueil
        require(ROOT . 'controllers/accueil.php');
        //On vérifie si la page n'est pas une page ajoutée par un addon
      }else if (isset($param[1]) && !empty($param[1]) && in_array($param[0], $controleur_def->getAvailableAddons()) && is_file(ROOT . 'addons/' . $param[0] . "/controllers/" . strtolower($param[1]) . '.php')) {
        // On charge le projet PageBuilder aussi pour les addons pour qu'ils puissent s'en servir.
        //On charge le pagebuilder abstract
        require_once(ROOT . "models/pageBuilders/AdminBuilder/init.php");
        //On charge celui du thème
        require_once(ROOT . 'views/themes/'. $Serveur_Config['theme'] . "/src/AdminBuilder/init.php");
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
ob_end_flush();


//                                                                 AA     LL    
//                 pzr--}fC/][dn1wYnfJ                            AAAA    LL      
//              c{?_x[v){Ur}][ft(vX(|wzrmOcQ                     AA |AA   LL      
//            #][_j(L}u-[[{x[{)q|)fztfXxUQuJkUm                 AAAAAAA   LL  
//        .])-1-U)t}-v})}}]L/(}hZjjmtXdYCZuCkJCnLC|Xx          |AA   AAA  LLLLLLLL   
//      I{(}11_-+/-q{p}f[1Oj{){QnfL|J00kC*#aqh0zQnjvwz/fCj^                       
//    "tf1f(fxtujXzt(jcf{{vX/t|bOxJ8hmJ0zwLwJCqzCxYLrYXXYcxx?                     
//   >()vxcJpUaXMUfdtcuj{]XrtrkkQC0#WhwkhokwCCzChbdcQB@BuO|vxn[                   
//  "f(|xcOhW&OB@B*BMvJ//(cCzXmqzhB%Mo*qrbUZLbma0&paB@B&X*Zw)xruLx/"              
//  rv/    a%Mw88(  ./jujn0nvu   mh%%B%B%ad*Qk0Y0#B@@@@@@*puQqdt/ntCujx/)   
// .xu  O  oh88c  .`.1ptL|OXY   @@@@c   #mJhd_        BBqhoLYkd#ju/j|rtnfnrjxjx~'
// `YuO  doBa`    nOCOXmQhmw    @@@B"    #ozbJk,         {?{jb*QbO#W*8*#mha0LxfIl`
//  cCZwdk&,      jzzQcak*u.    %%%%B    WoddJmO_            ddddbM*p/U0dbM*p/Ueudk                                              
//   hMW          J[fY:          wBB8    M*p/U0{                 wLwJCqzCxYwLwJCqzfdfd            
//                !qv              !qv   `}czjf:                    wLwJCqzCxYwLwJCddddfd      
//
