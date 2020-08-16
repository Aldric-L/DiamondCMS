<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

// Si l'on passe en mode modification des fichiers config (requettes POST via AJAX)
if (isset($param[2]) && !empty($param[2]) && isset($param[3]) && !empty($param[3]) && $param[2] == "write" ){
    define('FORCE_INLINE_ERR', true);
    //Si on modfifie le fichier config.ini
    if ($param[3] == "mainconf"){
        //On vérifie que chaque champ dont la modification est possible a bien été renvoyé
        if (isset($_POST['Serveur_name']) &&
            isset($_POST['protocol']) &&
            isset($_POST['desc']) &&
            isset($_POST['about_footer']) &&
            isset($_POST['support_en']) &&
            isset($_POST['vote_en']) &&
            isset($_POST['lien_vote']) &&
            isset($_POST['socialgl']) &&
            isset($_POST['socialyt']) &&
            isset($_POST['socialfb']) &&
            isset($_POST['socialtw']) &&
            isset($_POST['socialdiscord']) &&
            isset($_POST['favicon']) &&
            isset($_POST['logo']) ){
            //Ecriture dans le fichier ini
                //Copie du fichier dans un array temporaire
                $temp_conf = $Serveur_Config;
                //On modifie l'array temporaire
                $temp_conf['Serveur_name'] = $_POST['Serveur_name'];
                $temp_conf['protocol'] = $_POST['protocol'];
                $temp_conf['desc'] = $_POST['desc'];
                $temp_conf['about_footer'] = $_POST['about_footer'];
                //var_dump($_POST);
                if ($_POST['support_en'] == "true"){
                    $temp_conf['en_support'] = 1;
                }else {
                    $temp_conf['en_support'] = 0;
                }
                if ($_POST['vote_en'] == "true"){
                    if ($temp_conf['en_vote'] == 0 && !$controleur_def->addPage(true, "voter", "Voter")){
                        $controleur_def->addError(350);
                    }
                    $temp_conf['en_vote'] = 1;
                }else {
                    if ($temp_conf['en_vote'] == 1 && !$controleur_def->delPage(true, "voter")){
                        $controleur_def->addError(350);
                    }
                    $temp_conf['en_vote'] = 0;
                }
                if ($_POST['logo'] == "name_server"){
                    $temp_conf['logo_img'] = "0";
                    $temp_conf['name_logo'] = $_POST['logo'];
                }else {
                    $temp_conf['logo_img'] = "1";
                    $temp_conf['name_logo'] = $_POST['logo'];
                }
                $temp_conf['favicon'] = $_POST['favicon'];
                //$temp_conf['en_vote'] = $_POST['vote_en'];
                //$temp_conf['en_support'] = $_POST['support_en'];
                $temp_conf['lien_vote'] = $_POST['lien_vote'];
                $temp_conf['Social']['gl'] = $_POST['socialgl'];
                $temp_conf['Social']['yt'] = $_POST['socialyt'];
                $temp_conf['Social']['fb'] = $_POST['socialfb'];
                $temp_conf['Social']['tw'] = $_POST['socialtw'];
                $temp_conf['Social']['discord'] = $_POST['socialdiscord'];
                //On appel la class ini pour réecrire le fichier
                require_once(ROOT.'models/ini.php');
                $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
                //On lui passe l'array modifié
                $ini->ajouter_array($temp_conf);
                //On écrit en lui demmandant de conserver les groupes
                $ini->ecrire(true);
            //FIN Encriture ini
            die('Success');
            }
    //Si on modfifie le fichier bdd.ini  
    }else if ($param[3] == "bddconf"){
        if ( isset($_POST['host']) &&
             isset($_POST['db']) &&
             isset($_POST['usr']) &&
             isset($_POST['pwd']) ){
            $controleur_def->getBDD()->changeConfig($_POST['host'], $_POST['db'], $_POST['usr'], $_POST['pwd']);
            die('Success');
        }
    
    //Si on modifie la config des serveurs, pour en ajouter un
    }else if (defined("DServerLink") && DServerLink == true && $param[3] == "serveurs" && isset($param[4]) && !empty($param[4]) && $param[4] == "new"){
        if ( isset($_POST['name']) &&
             isset($_POST['desc']) &&
             isset($_POST['host']) &&
             isset($_POST['queryport']) &&
             isset($_POST['rconport']) &&
             isset($_POST['password']) &&
             isset($_POST['version']) &&
             isset($_POST['enabled']) &&
             isset($_POST['game']) &&
             isset($_POST['img']) ){
                
                $newconf = $cm->getConfig();
                $nbservers = 0;
                foreach($newconf as $c){
                    $nbservers = $nbservers+1;
                }
                $newconf[$nbservers+1] = array( "id" => $nbservers+1, "game" => $_POST['game'], "version" => $_POST['version'], 
                "name" => $_POST['name'], "desc" => $_POST['desc'], "img" => $_POST['img'],
                "host" => $_POST['host'], "queryport" => $_POST['queryport'], "port" => $_POST['rconport'],
                "password" => $_POST['password'], "enabled" => $_POST['enabled']);
                
                $cm->editConfig($newconf);

            die('Success');
        }
    //Si on modifie la config des serveurs, pour en supprimer un
    }else if (defined("DServerLink") && DServerLink == true && $param[3] == "serveurs" && isset($param[4]) && !empty($param[4]) && $param[4] == "supp"){
        if ( isset($_POST['id']) ){
                
                $newconf = $cm->getConfig();
                unset($newconf[intval($_POST['id'])]);
                foreach ($newconf as $k => $nc){
                    if ($nc['id'] > $_POST['id']){
                        $newconf[$k]['id'] = $newconf[$k]['id']-1;
                        $newconf[$k-1] = $newconf[$k];
                    }
                }
                unset($newconf[sizeof($newconf)]);
                
                //Il faut bien penser aussi aux conséquences, comme la suppression des commandes associées
                simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_cmd", array(array("server", "=", $_POST['id'])));

                $cm->editConfig($newconf);

            die('Success');
        }
    //Si on modifie la config des serveurs
    }else if (defined("DServerLink") && DServerLink == true && $param[3] == "serveurs" && isset($param[4]) && !empty($param[4])){
        if ( isset($_POST['name']) &&
             isset($_POST['desc']) &&
             isset($_POST['host']) &&
             isset($_POST['queryport']) &&
             isset($_POST['rconport']) &&
             isset($_POST['password']) &&
             isset($_POST['version']) &&
             isset($_POST['enabled']) &&
             isset($_POST['game']) &&
             isset($_POST['img']) ){
                
                $newconf = $cm->getConfig();
                $newconf[$param[4]] = array( "id" => $param[4], "game" => $_POST['game'], "version" => $_POST['version'], 
                "name" => $_POST['name'], "desc" => $_POST['desc'], "img" => $_POST['img'],
                "host" => $_POST['host'], "queryport" => $_POST['queryport'], "port" => $_POST['rconport'],
                "password" => $_POST['password'], "enabled" => $_POST['enabled']);
                
                $cm->editConfig($newconf);

            die('Success');
        }
    }
}

//Récupération de la configuration de la base de données
$bddconfig = $controleur_def->getBDD()->getConfig();

//Récupération de la config des serveurs => si Diamond-ServerLink est activé evidemment
if (DServerLink == true){
    $config_serveurs = $cm->getConfig();
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
}else {
    $config_serveurs = null;
}


$controleur_def->loadJS('admin/config');
$controleur_def->loadViewAdmin('admin/config/config', 'accueil', 'Configuration du CMS');