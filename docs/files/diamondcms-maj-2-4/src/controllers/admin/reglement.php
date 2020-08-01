<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

// Si l'on passe en mode modification des fichiers config (requettes POST via AJAX)
if (isset($_POST['content'])){
    if (isset($_POST['en_reglement']) && $_POST['en_reglement'] == "on"){
        //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $temp_conf = $Serveur_Config;
        if ($Serveur_Config['en_reglement'] == "1"){
            //On modifie l'array temporaire
            $temp_conf['en_reglement'] = "0";
        }else {
            //On modifie l'array temporaire
            $temp_conf['en_reglement'] = "1";
        }
        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini

        $fp = fopen (ROOT . "config/reglement.ftxt", "w");
        if (!$fp){
            $controleur_def->addError(111);
        }
        fseek ($fp, 0);
        fputs ($fp, $_POST['content']);
        fclose ($fp);
        $SCg = $temp_conf;

    }else {
        $fp = fopen (ROOT . "config/reglement.ftxt", "w");
        if (!$fp){
            $controleur_def->addError(111);
        }
        fseek ($fp, 0);
        fputs ($fp, $_POST['content']);
        fclose ($fp);
        $SCg = $Serveur_Config;
    }
}else { 
    $SCg = $Serveur_Config;
}
$current = file_get_contents(ROOT . "config/reglement.ftxt");
$controleur_def->loadViewAdmin('admin/config/reglement', 'accueil', 'Configuration du CMS - Page Reglement');