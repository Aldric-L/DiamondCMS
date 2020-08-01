<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

$config = $Serveur_Config;

// Si on passe en mode XHR pour activer ou désactiver la page
if (isset($param[2]) && !empty($param[2]) && $param[2] == "enable"){
    //Ecriture dans le fichier ini
    //Copie du fichier dans un array temporaire
    $temp_conf = $Serveur_Config;
    if ($Serveur_Config['en_faq'] == "1"){
        //On modifie l'array temporaire
        $temp_conf['en_faq'] = "0";
        if (!$controleur_def->delPage(true, "faq")){
            $controleur_def->addError(350);
        }
    }else {
        //On modifie l'array temporaire
        $temp_conf['en_faq'] = "1";
        if (!$controleur_def->addPage(true, "faq", "F.A.Q.")){
            $controleur_def->addError(350);
        }
    }
    //On appel la class ini pour réecrire le fichier
    $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
    //On lui passe l'array modifié
    $ini->ajouter_array($temp_conf);
    //On écrit en lui demmandant de conserver les groupes
    $ini->ecrire(true);
    //FIN Encriture ini
    $config = $temp_conf;
    die('Success');
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "delete" && isset($param[3]) && !empty($param[3])){
    if (simplifySQL\delete($controleur_def->bddConnexion(), "d_faq", array(array("id", "=", $param[3]))) != true){
        die('Error SQL');
    }else {
        die('Success');
    }
}

//Si on reçoit des informations dans la variable $_POST
if (isset($_POST) && !empty($_POST)){
    //Si le formulaire a bien été rempli entierement
    if (isset($_POST['question']) && !empty($_POST['question']) && isset($_POST['reponse']) && !empty($_POST['reponse'])){
        if (simplifySQL\insert($controleur_def->bddConnexion(), "d_faq", array("question", "reponse"), array($_POST['question'], $_POST['reponse'])) != true){
            $controleur_def->addError("342c");
        }
    }
}
$faq = simplifySQL\select($controleur_def->bddConnexion(), false, "d_faq", "*");
$controleur_def->loadJS("admin/faq");
$controleur_def->loadViewAdmin('admin/config/faq', 'accueil', 'Gestion de la FAQ');