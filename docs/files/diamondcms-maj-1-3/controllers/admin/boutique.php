<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

global $cm; 
//Si on reçoit des informations dans la variable $_POST (Ajout d'une catégorie)
if (isset($_POST) && !empty($_POST)){
    //Si le formulaire a bien été rempli entierement
    if (isset($_POST['new_cat']) && !empty($_POST['new_cat'])){
        if (simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_cat", array("name"), array($_POST['new_cat'])) != true){
            $controleur_def->addError("342c");
        }
    }
}
$boutique_config = parse_ini_file(ROOT . 'config/boutique.ini', true);

//Si on passe en mode XHR
if (isset($param[2]) && !empty($param[2]) && $param[2] == "xhr" && isset($param[3]) && !empty($param[3])){
    //Activer la boutique
    if ($param[3] == "enable"){
        //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $temp_conf = $Serveur_Config;
        if ($Serveur_Config['en_boutique']){
            //On modifie l'array temporaire
            $temp_conf['en_boutique'] = "0";
        }else {
            //On modifie l'array temporaire
            $temp_conf['en_boutique'] = "1";
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
    //suprimer une catégorie
    }else if ($param[3] == "delete" && isset($param[4]) && !empty($param[4])){
        if (simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_cat", array(array("id", "=", $param[4]))) != true){
            $controleur_def->addError("341b");
            die('Error SQL');
        }else {
            die('Success');
        }
    //suprimer un article
    }else if ($param[3] == "delete_article" && isset($param[4]) && !empty($param[4])){
        $id_cat = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "id, cat, img", array(array("id", "=", $param[4])));
        if (!empty($id_cat)){
            $nb_cat = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cat", "nb_articles", array(array("id", "=", $id_cat['cat'])));                    
            if (empty($nb_cat)){
                die('Error');
            }
            if (!unlink(ROOT . 'views/uploads/img/' . $id_cat['img'])){
                $controleur_def->addError(540);                
            }
            if (simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_articles", array(array("id", "=", $param[4]))) != true){
                $controleur_def->addError("341b");
                die('Error SQL');
            }else {
                if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_cat", 
                array(array("nb_articles", "=", intval($nb_cat['nb_articles'])-1)), 
                array( array( "id", "=", intval($id_cat['cat'])) ) ) ){
                    die('Error SQL');
                }
                if (simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_cmd", array(array("id_article", "=", $id_cat['id']))) != true){
                    $controleur_def->addError("341b");
                    die('Error SQL');
                }
                die('Success');
            }
        }else {
            die('Error');
        }
     
    //modifier un article
    }else if ($param[3] == "modify_article" && isset($param[4]) && !empty($param[4])){
        //On vérifie que des paramètres ont bien été envoyés
        if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['desc']) && isset($_POST['prix']) && isset($_POST['cat']) 
        && ( (!defined("DServerLink") || !DServerLink) || (defined("DServerLink") && DServerLink && isset($_POST['servers'])) ) ){
            //Modification de l'article en lui-même
            if ( !simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_articles", 
                    array(
                        array("name", "=", $_POST['name']),
                        array("description", "=", $_POST['desc']),
                        array("prix", "=", intval($_POST['prix'])),
                        array("cat", "=", $_POST['cat'])
                    ),
                    array(array("id", "=", $_POST['id'])) )){
                die('Error SQL');
            }
            if (defined("DServerLink") && DServerLink && isset($_POST['servers'])){
                $cmd = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cmd", "*", array(array("id_article", "=", $_POST['id'])));
                foreach($cmd as $c){
                    foreach($_POST['servers'] as $k => $s){
                        //Si il existe une commande pour ce serveur
                        if ($c['server'] == $s[0]){
                            //Si on choisit d'éxecuter une cmd sur ce serveur
                            if ($s[1] == "true"){
                                if ($s[2] == "true"){
                                    $s[2] = 1;
                                }else {
                                    $s[2] = 0;
                                }
                                //On modifie l'ancienne commande
                                if ( !simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_cmd", 
                                        array(
                                            array("cmd", "=", $s[3]),
                                            array("connexion_needed", "=", $s[2])
                                        ),
                                        array(array("id", "=", $c['id'])) )){
                                    die('Error SQL');
                                }
                            //Sinon on supprime l'ancienne commande
                            }else {
                                if ( !simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_cmd", array(array("id", "=", $c['id'])) )){
                                    die('Error SQL');
                                }
                            }
                            //Si on a traité cette commmande, on l'indique pour ne pas la renvoyer dans la BDD
                            $_POST['servers'][$k]['done'] = true;
                        }
                    }
                }

                //On traite toutes les commandes
                foreach($_POST['servers'] as $k => $s){
                    //Si la commande est bien activée et si on a pas déjà traité la demande
                    if ($s[1] == "true" && $s[3] != "null" && (!isset($_POST['servers'][$k]['done']) || !$_POST['servers'][$k]['done'])){
                        if ($s[2]){
                            $s[2] = 1;
                        }else {
                            $s[2] = 0;
                        }
                        if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_cmd", 
                            array("connexion_needed", "server", "id_article", "cmd"), 
                            array($s[2], $s[0], $_POST['id'], $s[3])
                        )){
                            die('Error SQL');
                        }
                    }
                }
            }

        }else {
            die('Error : paramètres');
        }
        
        die('Success');

    //Si on supprime une offre PayPal
    }else if ($param[3] == "delete_offre" && isset($param[4]) && !empty($param[4])){
        if (simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_paypal_offres", array(array("id", "=", $param[4]))) != true){
            $controleur_def->addError("341b");
            die('Error SQL');
        }
        die('Success');
    
    //Si on modifie les réglages PayPal
    }else if ($param[3] == "configpaypal" && !empty($_POST) && isset($_POST['en_paypal']) && isset($_POST['sandbox']) && isset($_POST['money']) && isset($_POST['id_pp']) && isset($_POST['secret_pp'])){
        $tmp_boutique = $boutique_config;
        $tmp_boutique['PayPal']['en_paypal'] = $_POST['en_paypal'];
        $tmp_boutique['PayPal']['sandbox'] = $_POST['sandbox'];
        $tmp_boutique['PayPal']['money'] = $_POST['money'];
        $tmp_boutique['PayPal']['id'] = $_POST['id_pp'];
        $tmp_boutique['PayPal']['secret'] = $_POST['secret_pp'];

        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/boutique.ini", 'Configuration de la boutique de DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($tmp_boutique);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini
        $boutique_config = $tmp_boutique;
        die('Success');

    //Si on modifie les réglages DediPass
    }else if ($param[3] == "configddp" && !empty($_POST) && isset($_POST['en_ddp']) && isset($_POST['pub_key']) && isset($_POST['priv_key'])){
        $tmp_boutique = $boutique_config;
        $tmp_boutique['DediPass']['en_ddp'] = $_POST['en_ddp'];
        $tmp_boutique['DediPass']['public_key'] = $_POST['pub_key'];
        $tmp_boutique['DediPass']['private_key'] = $_POST['priv_key'];

        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/boutique.ini", 'Configuration de la boutique de DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($tmp_boutique);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini
        $boutique_config = $tmp_boutique;
        die('Success');
    
    //Si on modifie les réglages généraux
    }else if ($param[3] == "saveconf" && !empty($_POST) && isset($_POST['money_sym']) && isset($_POST['money_name']) && isset($_POST['money'])){
        //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $temp_conf = $Serveur_Config;
        $temp_conf['money'] = $_POST['money_sym'];
        $temp_conf['money_name'] = $_POST['money_name'];
        $temp_conf['Serveur_money'] = $_POST['money'];

        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/config.ini", 'Configuration DiamondCMS');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
        //FIN Encriture ini
        $config = $temp_conf;
        die('Success');
        
    //Si on ajoute une offre PayPal
    }else if ($param[3] == "addpaypal" && !empty($_POST) && isset($_POST['name']) && isset($_POST['nb']) && isset($_POST['prix'])){
        if (simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_paypal_offres", 
            array("name", "price", "tokens", "uuid"), 
            array($_POST['name'], $_POST['prix'], $_POST['nb'], uniqid())) != true){
            $controleur_def->addError("342c");
            die('SQL: Error');
        }
        die('Success');
    }

//Si on charge le gestionnaire d'articles
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "articles"){
    if (isset($_POST['name']) && !empty($_POST['name']) &&
        isset($_POST['desc']) && !empty($_POST['desc']) &&
        isset($_POST['prix']) && !empty($_POST['prix']) &&
        isset($_POST['cat']) && !empty($_POST['cat'])){
    
        //Le formulaire de "base" est complet, analysons si une image a bien été envoyée, et étudions la config pour le lien avec les serveurs
        if (isset($_FILES['img']) && $_FILES['img']['size'] != 0){
            //S'il s'agit bien d'une image
            if (strrpos($_FILES['img']['type'], "image/") === false){
                $controleur_def->addError(524);
            }else {
                $upload = uploadFile('img', "boutique");
                //S'il y a une erreur d'en l'upload
                if (is_int($upload)){
                    $controleur_def->addError(500 + intval($upload));
                }else {
                  $filename = $upload;
                  if ( !simplifySQL\insert( $controleur_def->bddConnexion(), "d_boutique_articles", 
                  array('name', 'description', 'img', 'prix', 'date_ajout', 'cat'), 
                  array($_POST['name'], $_POST['desc'], $filename, $_POST['prix'], date("Y-m-d"), $_POST['cat']) ) ){
                        $controleur_def->addError("342c");
                  }else {
                    //On modifie le nombre d'articles de la catégorie 
                    $cat_nb_articles = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cat", "id, nb_articles", array(array("id", "=", $_POST['cat'])));
                    if (!empty($cat_nb_articles)){
                        if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_cat", array(array("nb_articles", "=", intval($cat_nb_articles['nb_articles'])+1)), array( array( "id", "=", intval($_POST['cat']) ) ) )){
                            $controleur_def->addError("342a");                            
                        }else {
                            $id = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "*", array(array("id", ">=", "all")), "id", true);
                            if (!empty($id) && isset($id['id'])){
                                //On s'occupe des commandes maintenant
                                if (defined("DServerLink") && DServerLink == true){
                                    $serveurs = $cm->getConfig();
                                    foreach($serveurs as $s){
                                        //Si le serveur est activé et qu'une commande a été entrée
                                        if ($s['enabled'] && isset($_POST[$s['id'] . '_cmd']) && !empty($_POST[$s['id'] . '_cmd']) && $_POST[$s['id'] . '_cmd'] != null){
                                            //on vérifie que l'utilisateur veut bien envoyer une commande
                                            if (isset($_POST[$s['id'] . '_en_serveur']) && !empty($_POST[$s['id'] . '_en_serveur']) && $_POST[$s['id'] . '_en_serveur'] == "on"){
                                                $mustbeconnected = false;
                                                if (isset($_POST[$s['id'] . '_mustbe_connected']) && !empty($_POST[$s['id'] . '_mustbe_connected']) && $_POST[$s['id'] . '_mustbe_connected'] == "on"){
                                                    $mustbeconnected = true;
                                                }
                                                //On enregistre donc bien la commande
                                                if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_cmd",
                                                array("cmd", "connexion_needed", "server", "id_article"),
                                                array($_POST[$s['id'] . '_cmd'], $mustbeconnected, $s['id'], $id['id']))){
                                                    $controleur_def->addError("342c");                                            
                                                }
                                            }
                                        }
                                    }
                                }
                            }else {
                                $controleur_def->addError(121);                        
                            }
                        }
                    }else {
                        $controleur_def->addError(121);       
                    }
                  }

                  
                }
            } 
        }else {
            $controleur_def->addError(140);            
        } 
    }else if (isset($_POST) && !empty($_POST)){
        $controleur_def->addError(140);
    }

    if ($Serveur_Config['en_boutique']){
        if (defined("DServerLink") && DServerLink == true){
            $serveurs = $cm->getConfig();
        }
        $cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cat" ,"*");
        foreach ($cats as $key => $cat){
            $cats[$key]['articles'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_articles" ,"*", array(array("cat", "=", $cats[$key]['id'])));
            foreach ($cats[$key]['articles'] as $k => $c){
                if (strpos($cats[$key]['articles'][$k]['img'], "png") !== false) {
                    $cats[$key]['articles'][$k]['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/png/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/200/200';
                }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpg") !== false) {
                    $cats[$key]['articles'][$k]['link'] =  $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/200/200';
                }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpeg") !== false) { 
                    $cats[$key]['articles'][$k]['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpeg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -5) . '/200/200';
                }
                //On récupère les commandes associées
                $cats[$key]['articles'][$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cmd", "*", array(array("id_article", "=", $cats[$key]['articles'][$k]['id'])));
            }
        }                
        $config = $Serveur_Config;
        $controleur_def->loadJS('admin/boutique/articles');
        $controleur_def->loadViewAdmin('admin/boutique/articles', 'accueil', "Gestion des articles");
        die;
    }else {
        $controleur_def->loadViewAdmin('admin/boutique/disabled', 'accueil', "Boutique désactivée");
        die;
    }
//Si on charge les tâches et commandes à satisfaire
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "tasks"){
    if ($Serveur_Config['en_boutique']){
        if (defined("DServerLink") && DServerLink == true){
            $serveurs = $cm->getConfig();
        }

        //On récupère les 20 dernières tâches
        $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", false, 'id', true, array(0, 20));
        
        //On récupère les 20 dernières commandes
        $commandes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*", false, 'id', true, array(0, 20));

        foreach ($commandes as $key => $c){
            $article = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles" ,"*", array(array("id", "=", $commandes[$key]['id_article'])));
            if (empty($article)){
                $article['name'] = "Article inconnu";
            }
            $commandes[$key]['article'] = $article['name'];
            
            $user = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre" ,"*", array(array("id", "=", $commandes[$key]['id_user'])));
            if (empty($user)){
                $user['pseudo'] = "Utilisateur inconnu";
            }
            $commandes[$key]['user'] = $user['pseudo'];
        }

        foreach ($tasks as $k => $t){
            //On récupère la commande correspondante
            $tasks[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd']))); 
            if (!defined("DServerLink") || !DServerLink){
                $tasks[$k]['cmd']['server_name'] = "";
                $tasks[$k]['cmd']['server_game'] = "";
            }else {
                $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
                $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
            }
            
        }        

        $config = $Serveur_Config;
        $controleur_def->loadViewAdmin('admin/boutique/tasks', 'accueil', "Tâches et achats virtuels");
        die;
    }else {
        $controleur_def->loadViewAdmin('admin/boutique/disabled', 'accueil', "Boutique désactivée");
        die;
    }

//Si on charge le gestionnaire de PayPal
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "paypal"){
    if ($Serveur_Config['en_boutique']){
        $payments = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_paypal", "*", false, "id", true);
        $offres = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_paypal_offres", "*", false, "id", true);

        $controleur_def->loadJS('admin/boutique/paypal');
        $controleur_def->loadViewAdmin('admin/boutique/paypal', 'accueil', "Gestion de PayPal");
        die;
    }else {
        $controleur_def->loadViewAdmin('admin/boutique/disabled', 'accueil', "Boutique désactivée");
        die;
    }

//Si on charge le gestionnaire DediPass
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "dedipass"){
    if ($Serveur_Config['en_boutique']){
        $payments = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_dedipass", "*", false, "id", true);

        $controleur_def->loadJS('admin/boutique/dedipass');
        $controleur_def->loadViewAdmin('admin/boutique/dedipass', 'accueil', "Gestion de DediPass");
        die;
    }else {
        $controleur_def->loadViewAdmin('admin/boutique/disabled', 'accueil', "Boutique désactivée");
        die;
    }
}

$cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cat" ,"*");
//var_dump($cats);

//Par défaut on charge la configuration générale de la boutique
$config = $Serveur_Config;
$controleur_def->loadJS('admin/boutique/config');
$controleur_def->loadViewAdmin('admin/boutique/config', 'accueil', "Configuration de la Boutique");