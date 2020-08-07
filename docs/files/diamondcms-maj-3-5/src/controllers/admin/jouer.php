<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

// Si l'on passe en mode modification des fichiers config (requettes POST via AJAX)
if (isset($_POST['content'])){
    if (isset($_POST['en_jouer']) && $_POST['en_jouer'] == "on"){
        //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $temp_conf = $Serveur_Config;
        if ($Serveur_Config['en_jouer'] == "1"){
            //On modifie l'array temporaire
            $temp_conf['en_jouer'] = "0";
            if (!$controleur_def->delPage(true, "jouer")){
                $controleur_def->addError(350);
            }
        }else {
            //On modifie l'array temporaire
            $temp_conf['en_jouer'] = "1";
            if (!$controleur_def->addPage(true, "jouer", "Jouer")){
                $controleur_def->addError(350);
            }
        }
        $temp_conf['text_jouer_menu'] = $_POST['content'];
        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini
        $SCg = $temp_conf;
    }else {
        //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $temp_conf = $Serveur_Config;
        $temp_conf['text_jouer_menu'] = $_POST['content'];
        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini
        $SCg = $temp_conf;
    }
}else { 
    $SCg = $Serveur_Config;
}
$current = $SCg['text_jouer_menu'];
$controleur_def->loadViewAdmin('admin/config/jouer', 'accueil', 'Configuration du CMS - Modal Jouer');