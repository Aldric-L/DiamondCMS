<?php 
$controleur_def->loadModel('admin/accueil');

$nb_coms = sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_com", "id"));

$nb_ventes = sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*"));

$errors = sizeof($controleur_def->getErrorsInLog());
$errors_raw = $controleur_def->getErrorsInLog();

$errors_content = array();
$min = (sizeof($errors_raw) > 10) ? 10 : sizeof($errors_raw);
for ($i=sizeof($errors_raw); $i>sizeof($errors_raw)-$min; $i--){
    $errors_raw[$i-1] = array_merge($errors_raw[$i-1], $controleur_def->getError($errors_raw[$i-1]['code']));
    array_push($errors_content, $errors_raw[$i-1]);
}

$nb_tickets = sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_support_tickets", "id"));

$config = $Serveur_Config;

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

//Si on avait déjà vu que DiamondCMS n'était pas à jour
if (file_exists(ROOT . 'config/outdated.dcms')){
  $outdated = true;
  $od = parse_ini_file(ROOT . "config/outdated.dcms", true);
  if (!empty($od) && isset($od['version_int']) && DCMS_INT_VERSION >= intval($od['version_int'])){
    unlink(ROOT . 'config/outdated.dcms');
    $outdated = false;
  }
}

//On vérifie que TinyMCE est installé correctement
$conf_mce = cleanIniTypes(parse_ini_file(ROOT . "config/tinymce.ini", true));
if (($conf_mce['editor']['key'] == $conf_mce['editor']['def_key'] OR empty($conf_mce['editor']['key'])) && $conf_mce['editor']['enable']){
  $mce_error = true;
}else {
  $mce_error = false;
}

$controleur_def->loadJS('admin/accueil');
$controleur_def->loadViewAdmin('admin/accueil', 'accueil', 'Accueil');
