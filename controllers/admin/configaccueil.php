<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

$config = $Serveur_Config;

if (!empty($_POST)){
    if (isset($_POST['titre-1']) && isset($_POST['content-1']) && isset($_POST['img_1']) && isset($_POST['fa-1']) &&
        isset($_POST['titre-2']) && isset($_POST['content-2']) && isset($_POST['img_2']) && isset($_POST['fa-2']) &&
        isset($_POST['titre-3']) && isset($_POST['content-3']) && isset($_POST['img_3']) && isset($_POST['fa-3']) ){
            //Ecriture dans le fichier ini
            //Copie du fichier dans un array temporaire
            $config = $Serveur_Config;
            //On modifie l'array temporaire
            $config['Accueil']['titre_1'] = $_POST['titre-1'];
            $config['Accueil']['titre_2'] = $_POST['titre-2'];
            $config['Accueil']['titre_3'] = $_POST['titre-3'];
            $config['Accueil']['desc_1'] = $_POST['content-1'];
            $config['Accueil']['desc_2'] = $_POST['content-2'];
            $config['Accueil']['desc_3'] = $_POST['content-3'];
            $config['Accueil']['img_1'] = $_POST['img_1'];
            $config['Accueil']['img_2'] = $_POST['img_2'];
            $config['Accueil']['img_3'] = $_POST['img_3'];
            $config['Accueil']['fa_1'] = $_POST['fa-1'];
            $config['Accueil']['fa_2'] = $_POST['fa-2'];
            $config['Accueil']['fa_3'] = $_POST['fa-3'];
            
            require_once(ROOT.'models/ini.php');
            $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
            //On lui passe l'array modifié
            $ini->ajouter_array($config);
            //On écrit en lui demmandant de conserver les groupes
            $ini->ecrire(true);
            //FIN Encriture ini
            die('Success');
    }else if (isset($_POST['en_whois']) && isset($_POST['en_news']) && isset($_POST['bg']) ){
        //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $config = $Serveur_Config;
        //On modifie l'array temporaire
        $config['Accueil']['en_whois'] = $_POST['en_whois'];
        $config['Accueil']['en_news'] = $_POST['en_news'];
        $config['bg'] = $_POST['bg'];
                
        require_once(ROOT.'models/ini.php');
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($config);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini
        die('Success');
    }

}

$img_available = array();
    //On liste aussi les images disponibles dans le dossier img
    if($dossier = opendir(ROOT . 'views/uploads/img')){
        while(false !== ($fichier = readdir($dossier))){
            if($fichier != '.' && $fichier != '..' && !is_dir(ROOT . 'views/uploads/img/' . $fichier)){
                array_push($img_available, $fichier);
            }
        }
    }else {
        $controleur_def->addError(111);
    }

$controleur_def->loadJS('admin/configaccueil');
$controleur_def->loadViewAdmin('admin/config/accueil', 'accueil', 'Configuration de l\'Accueil');