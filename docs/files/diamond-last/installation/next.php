<?php 
//On commence par récupérer l'étape
require_once(ROOT.'models/DiamondCore/ini.class.php');
$Serveur_Config = parse_ini_file(ROOT . "config/config.ini", true);
$step = $Serveur_Config['install_step'];
$step = $step+1;
if ($step == 5){
    //Ecriture dans le fichier ini
    //Copie du fichier dans un array temporaire
    $temp_conf = $Serveur_Config;
    $temp_conf['install_step'] = 5;
    $temp_conf['is_install'] = 1;
    $temp_conf['date_install'] = date("j/n/Y");
    $temp_conf['id_cms'] = sha1(uniqid() . "_". date("j/n/Y"));
    $error = "";
    try {
        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
    }catch (Exception $e){
        $error = "Code " . $e->getCode() . " - " . $e->getMessage();
    }
    
    if ($error == ""){
        die('Success');
    }else {
        die('Error: ' . $error);
    }
}
//Ecriture dans le fichier ini
//Copie du fichier dans un array temporaire
$temp_conf = $Serveur_Config;
$temp_conf['install_step'] = $step;

$error = "";
try {
    //On appel la class ini pour réecrire le fichier
    $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
    //On lui passe l'array modifié
    $ini->ajouter_array($temp_conf);
    //On écrit en lui demmandant de conserver les groupes
    $ini->ecrire(true);
}catch (Exception $e){
    $error = "Code " . $e->getCode() . " - " . $e->getMessage();
}
    
if ($error == ""){
    die('Success');
}else {
    die('Error: ' . $error);
}