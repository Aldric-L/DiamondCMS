<?php 
//On gère les erreurs et exceptions (code issu de l'index.php)
define('FORCE_INLINE_ERR', true);

$Serveur_Config = parse_ini_file(ROOT . 'config/config.ini', true);
if (isset($_POST['Serveur_name']) &&
    isset($_POST['protocol']) &&
    isset($_POST['desc']) &&
    isset($_POST['about_footer'])){
    //Ecriture dans le fichier ini
    //Copie du fichier dans un array temporaire
    $temp_conf = $Serveur_Config;
    //On modifie l'array temporaire
    $temp_conf['Serveur_name'] = $_POST['Serveur_name'];
    $temp_conf['protocol'] = $_POST['protocol'];
    $temp_conf['desc'] = $_POST['desc'];
    $temp_conf['about_footer'] = $_POST['about_footer'];
    //On appel la class ini pour réecrire le fichier
    $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
    //On lui passe l'array modifié
    $ini->ajouter_array($temp_conf);
    //On écrit en lui demmandant de conserver les groupes
    $ini->ecrire(true);
    //FIN Encriture ini
    die('Success');
}