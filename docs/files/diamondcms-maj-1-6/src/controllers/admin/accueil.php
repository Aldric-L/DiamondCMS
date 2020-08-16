<?php 
$controleur_def->loadModel('admin/accueil');

$nb_coms = sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_com", "id"));

$nb_ventes = sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*"));

$errors = getNumberErrorLog();

$errors_content = analiserLog($controleur_def, 10);
$nb_tickets = sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_support_tickets", "id"));

if (defined("DServerLink") && DServerLink){
    $n_serveurs = $servers_link->getNbServers();
}

$config = $Serveur_Config;

if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2]) && $param[2] == 'theme' && isset($param[3]) && !empty($param[3])){
    if (file_exists(ROOT . 'views/themes/' . $param[3] . '/theme.ini')){
      define('FORCE_INLINE_ERR', true);
        $temp_conf = $Serveur_Config;
        $temp_conf['theme'] = $param[3];
        //On appel la class ini pour réecrire le fichier
        require_once(ROOT.'models/ini.php');
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        $config = $temp_conf;
        die('Success');
    }

}else if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2]) && $param[2] == 'addon' && isset($param[3]) && !empty($param[3])){
  define('FORCE_INLINE_ERR', true);
    if (file_exists(ROOT . 'addons/' . $param[3] . '/init.php')){
      if (file_exists(ROOT . 'addons/' . $param[3] . '/disabled.dcms')){
        unlink(ROOT . 'addons/' . $param[3] . '/disabled.dcms');
      }else {
        $file = fopen(ROOT . 'addons/' . $param[3] . '/disabled.dcms', 'x');
        fclose($file);
      }
    }
    die('Success');

}else if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2]) && $param[2] == 'mtnc'){
  define('FORCE_INLINE_ERR', true);
    $temp_conf = $Serveur_Config;
    if ($Serveur_Config['mtnc'] == "true"){
      $temp_conf['mtnc'] = "false";
    }else {
      $temp_conf['mtnc'] = "true";
    }
    //On appel la class ini pour réecrire le fichier
    require_once(ROOT.'models/ini.php');
    $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
    //On lui passe l'array modifié
    $ini->ajouter_array($temp_conf);
    //On écrit en lui demmandant de conserver les groupes
    $ini->ecrire(true);
    $config = $temp_conf;
    die('Success');
//Désormais, la vérification de la mise à jour, comme le broadcast, est asynchrone
}else if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2]) && $param[2] == 'check-maj'){
  //Vérification de la version du CMS :
  $version = @file_get_contents('https://aldric-l.github.io/DiamondCMS/version.txt');
  if (!empty($version)){
    $version = intval($version);
    if (DCMS_INT_VERSION < $version){
      fopen(ROOT . 'config/outdated.dcms', 'w+');
      die('<strong><span style="color: red;">Attention !</span> DiamondCMS n\'est plus à jour.</strong> <a href="https://aldric-l.github.io/DiamondCMS/files/diamondcms-maj-' .DCMS_INT_VERSION . '-' . $version .'.zip">Télécharger l\'archive de la Mise à jour.</a>');
    }else {
      if (file_exists(ROOT . 'config/outdated.dcms')){
        unlink(ROOT . 'config/outdated.dcms');
      }
    }
  }
  die('DiamondCMS est à jour. Vérification effectuée le ' . date('d.m.Y à H:m:s'));

}

$themes = array();
  //Chargement des themes
  if ($dir = opendir(ROOT . 'views/themes/')) {
    while($file = readdir($dir)) {
      //On ouvre les sous-dossiers
      if(is_dir(ROOT . 'views/themes/' . $file) && !in_array($file, array(".",".."))) {
        if ($d = opendir(ROOT . 'views/themes/' . $file)) {
          while($f = readdir($d)) {
            //Dans ces sous-dossiers, on charge les fichiers nommés theme.ini qui s'occupent eux-même de charger les addons auquels ils appartiennent
            if ($f == "theme.ini"){
                $t = parse_ini_file(ROOT . 'views/themes/' . $file . '/'. $f);
                if ($t['name'] == $config['theme']){
                    $t['enabled'] = true;
                }else {
                    $t['enabled'] = false;  
                }
                if ($t['version_cms'] == DCMS_VERSION){
                    array_push($themes, $t);
                }
            }
          }
          closedir($d);
        }
      }
    }
    closedir($dir);
  }

$all_addons = array();
  //Chargement des addons
  if ($dir = opendir(ROOT . 'addons/')) {
    while($file = readdir($dir)) {
      //On ouvre les sous-dossiers
      if(is_dir(ROOT . 'addons/' . $file) && !in_array($file, array(".",".."))) {
        if ($d = opendir(ROOT . 'addons/' . $file)) {
          while($f = readdir($d)) {
            //Dans ces sous-dossiers, on charge les fichiers nommés init.php qui s'occupent eux-même de charger les addons auquels ils appartiennent
            if ($f == "init.php"){
              if (!file_exists(ROOT . 'addons/' . $file . '/disabled.dcms')){
                array_push($all_addons, array($file, false));
              }else {
                array_push($all_addons, array($file, true));
              }
            }
          }
          closedir($d);
        }
      }
    }
    closedir($dir);
  }

//Si on avait déjà vu que DiamondCMS n'était pas à jour
if (file_exists(ROOT . 'config/outdated.dcms')){
  $outdated = true;
}

$controleur_def->loadJS('admin/accueil');
$controleur_def->loadViewAdmin('admin/accueil', 'accueil', 'Accueil');
