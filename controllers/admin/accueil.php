<?php 
$controleur_def->loadModel('admin/accueil');

$nb_coms = getNActionsForum($controleur_def->bddConnexion());

$errors = getNumberErrorLog();

$errors_content = analiserLog($controleur_def, 10);

$nb_tickets = getNumberTickets($controleur_def->bddConnexion());

if (defined("DServerLink")){
    $n_serveurs = $servers_link->getNbServers();
}

$config = $Serveur_Config;

if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2]) && $param[2] == 'theme' && isset($param[3]) && !empty($param[3])){
    if (file_exists(ROOT . 'views/themes/' . $param[3] . '/theme.ini')){
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
}

$themes = array();
  //Chargement des addons
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

//$infos_cms = parse_ini_string(@file_get_contents($Serveur_Config['api_url'] . "status_cms.php?id=356a192b7913b04c54574d18c28d46e6395428ab"));
/*var_dump($infos_cms);
exit;*/
$controleur_def->loadJS('admin/accueil');
$controleur_def->loadViewAdmin('admin/accueil', 'accueil', 'Accueil');
