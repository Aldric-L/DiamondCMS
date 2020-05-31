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
    if ($Serveur_Config['en_forum']){
        //On modifie l'array temporaire
        $temp_conf['en_forum'] = "0";
    }else {
        //On modifie l'array temporaire
        $temp_conf['en_forum'] = "1";
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
    if (simplifySQL\delete($controleur_def->bddConnexion(), "d_forum_cat", array(array("id", "=", $param[3]))) != true){
        $controleur_def->addError("341b");
        die('Error SQL');
    }else {
        die('Success');
    }
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "delete_scat" && isset($param[3]) && !empty($param[3])){
    if (simplifySQL\delete($controleur_def->bddConnexion(), "d_forum_sous_cat", array(array("id", "=", $param[3]))) != true){
        $controleur_def->addError("341b");
        die('Error SQL');
    }else {
        die('Success');
    }
}
//var_dump($_POST, $_POST['en_fe']);
//Si on reçoit des informations dans la variable $_POST
if (isset($_POST) && !empty($_POST)){
    //Si le formulaire a bien été rempli entierement
    if (isset($_POST['new_cat']) && !empty($_POST['new_cat'])){
        if (simplifySQL\insert($controleur_def->bddConnexion(), "d_forum_cat", array("titre"), array($_POST['new_cat'])) != true){
            $controleur_def->addError("342c");
        }
    }else if (isset($_POST['new_scat']) && !empty($_POST['new_scat']) && isset($_POST['cat_id']) ){
        if (!empty(simplifySQL\select($controleur_def->bddConnexion(), true, "d_forum_sous_cat", "*", array(array('titre', "=", $_POST['new_scat']))))){
            $controleur_def->addError("150");
        }else {
            if (simplifySQL\insert($controleur_def->bddConnexion(), "d_forum_sous_cat", array("titre", "id_cat"), array($_POST['new_scat'], $_POST['cat_id'])) != true){
                $controleur_def->addError("342c");
            }
        }
        
    }else if (isset($_POST['en_fe']) && !empty($_POST['en_fe']) && isset($_POST['link']) && !empty($_POST['link'])){
        //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $temp_conf = $Serveur_Config;
        if ($_POST['en_fe'] == "true"){
            //On modifie l'array temporaire
            $temp_conf['other_forum'] = "1";
        }else {
            //On modifie l'array temporaire
            $temp_conf['other_forum'] = "0";
        }
        $temp_conf['link_forum'] = $_POST['link'];
        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini
        $config = $temp_conf;
        die('Success');
    }
}
$cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_cat", "*");
foreach($cats as $k => $c){
    $cats[$k]['nb'] = sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum", "id_scat", array(array("id_scat", "=", $cats[$k]['id']))));
}
$scats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_sous_cat", "*", false, "id_cat");
foreach ($scats as $k => $s){
    foreach($cats as $p => $c){
        if (intval($cats[$p]['id']) == intval( $scats[$k]['id_cat'])){
            $scats[$k]['cat_name'] = $cats[$p]['titre'];
        }
    }
}
$controleur_def->loadJS("admin/forum");
$controleur_def->loadViewAdmin('admin/config/forum', 'accueil', 'Gestion du forum');