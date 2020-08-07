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
    define('FORCE_INLINE_ERR', true);
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
            //On met en archive tous les articles de la catégorie
            @simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_articles", array(array("archive", "=", 1)), array(array("cat", "=", $param[4])));
            die('Success');
        }
    //suprimer un article
    }else if ($param[3] == "delete_article" && isset($param[4]) ){
        $id_cat = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "id, cat, img", array(array("id", "=", $param[4])));
        if (!empty($id_cat)){
            $nb_cat = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cat", "nb_articles", array(array("id", "=", $id_cat['cat'])));                    
            if (empty($nb_cat)){
                die('Error');
            }
            if (!unlink(ROOT . 'views/uploads/img/' . $id_cat['img'])){
                $controleur_def->addError(540);                
            }
            //On vérifie si cet article a déjà été acheté
            $commandes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*", array(array("id_article", "=", $param[4])));
            if ($commandes !=  false && !empty($commandes)){
                //On archive alors l'article
                if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_articles", array(array("archive", "=", 1)), array(array("id", "=", $param[4])))){
                    $controleur_def->addError("341b");
                    die('Error SQL');
                }
            //Si l'article n'a jamais été acheté
            }else {
                //On le supprime
                if (simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_articles", array(array("id", "=", $param[4]))) != true){
                    $controleur_def->addError("341b");
                    die('Error SQL');
                }
            } 
            //Dans tous les cas, on modifie le nb d'article dans la catégorie, et on supprime toutes les taches associées/
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
        }else {
            die('Error');
        }
     
    //modifier un article
    }else if ($param[3] == "modify_article" && isset($param[4]) && !empty($param[4])){
        //On vérifie que des paramètres ont bien été envoyés
        if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['desc']) && isset($_POST['prix']) && isset($_POST['cat']) ){
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

            //On s'occupe des commandes maintenant
            //Depuis la 1.1 : Nouveau système avec les commandes manuelles
            $nb_man_tasks = intval($_POST['nb_man_tasks']);
            $nb_auto_tasks = intval($_POST['nb_auto_tasks']);
            //On commence par les tâches manuelles
            if ($nb_man_tasks > 0){
                for ($i = 0; $i < $nb_man_tasks; $i++){
                    if (isset($_POST['man_cmd'][$i]) && !empty($_POST['man_cmd'][$i])){
                        if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_cmd",
                                array("cmd", "connexion_needed", "id_article", "is_manual"),
                                array($_POST['man_cmd'][$i], false, $_POST['id'], 1))){
                                     $controleur_def->addError("342c"); 
                                     die('Error: 342c');                                           
                        }
                    }
                }
            }

            //On traite ensuite les tâches automatiques en vérifiant d'abord si DSL est bien activé
            if (defined("DServerLink") && DServerLink == true && $nb_auto_tasks > 0){
                $serveurs = $cm->getConfig();
                for ($i = 0; $i < $nb_auto_tasks; $i++){
                    if (isset($_POST['cmd'][$i]) && !empty($_POST['cmd'][$i]) &&
                    isset($_POST['server'][$i]) && !empty($_POST['server'][$i])){
                        //On commence par vérifier que le serveur demandé existe et est bien activé
                        if (isset($serveurs[intval($_POST['server'][$i])]) && $serveurs[intval($_POST['server'][$i])]['enabled']){
                            //Si c'est le cas, on regarde si on doit obliger le joueur à être connecté pour recevoir son dû
                            $mustbeconnected = false;
                            if (is_array($_POST['mustbe_connected']) && isset($_POST['mustbe_connected'][$i]) && !empty($_POST['mustbe_connected'][$i]) && $_POST['mustbe_connected'][$i] == "on"){
                                $mustbeconnected = true;
                            }
                            //On enregistre donc bien la commande
                            if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_cmd",
                            array("cmd", "connexion_needed", "server", "id_article", "is_manual"),
                            array($_POST['cmd'][$i], $mustbeconnected, $_POST['server'][$i], $_POST['id'], 0))){
                                $controleur_def->addError("342c");                                            
                                die('Error: 342c');   
                            }
                        }else {
                            $controleur_def->addError(480);
                            die('Error: 480');   
                        }
                    }
                }
            }
            

        }else {
            die('Error : paramètres');
        }
        
        die('Success');

    //Si on supprime une tâche
    }else if ($param[3] == "delete_task" && isset($param[4]) && !empty($param[4])){
        //On commence par vérifier que la tâche n'est pas associée à une commande
        $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array( array( "cmd", "=", intval($param[4]) ) ));
        if (!empty($tasks)){
            foreach ($tasks as $task){
                if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_todolist", 
                array(
                    array("done", "=", 1), 
                    array("date_done", "=", date("Y-m-d H:i:s")),
                    array("stopped", "=", 1),
                    array("stopped_reason", "=", "La tâche a été supprimée de l'article. (" . $_SESSION['user']->getPseudo() . ").")
                ), array(array("id", "=", $task['id'])))){
                    die('Error');
                }
                //Mais, attention, si c'est la dernière tâche, on doit clore la commande
                $ts = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $task['id_commande']), "AND", array("done", "=", 0)));
                if (empty($ts)){
                    if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_achats", 
                        array(
                            array("success", "=", 1)
                        ), array(array("id", "=", $task['id_commande'])))){
                            die('Error: Impossible de clore la commande');
                    }
                }
            }
            //Comme la tache sert encore, on l'archive
            if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_cmd", 
                array(
                    array("archive", "=", 1)
                ), array(array("id", "=", $param[4] )))){
                    die('Error');
                }
        }else {
            //Si la tâche est associée à aucune commande, on peut la supprimer purement et simplement.
            if (simplifySQL\delete($controleur_def->bddConnexion(), "d_boutique_cmd", array(array("id", "=", $param[4]))) != true){
                $controleur_def->addError("341b");
                die('Error SQL');
            }
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

//Si on passe en mode IFRAME pour montrer le statut de la commande
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "iframe" && isset($param[3]) && !empty($param[3])){
    $uuid = $param[3];
    //On essaye de charger la commande 
    $commande = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_achats", "*", array(array("uuid", "=", $uuid)));
    if (empty($commande)){
        $erreur = "Impossible de charger la commande demandée. Numéro de commande transmis : " . $uuid;
        die('Erreur');
    }
    
    //On récupère aussi l'article 
    $article = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "*", array(array("id", "=", $commande['id_article'])));

    //On charge les taches automatiques à faire
    $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']),  "AND", array("done", "=", 0)));
    $death_list = array();
    foreach ($tasks as $k => $t){
        //On récupère la commande correspondante
        $tasks[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));
        if ($tasks[$k]['cmd']['is_manual'] == true){
            array_push($death_list, $k);
        }
        if (!defined("DServerLink") || !DServerLink){
            $tasks[$k]['cmd']['server_name'] = "";
            $tasks[$k]['cmd']['server_game'] = "";
        }else if ($tasks[$k]['cmd']['is_manual'] == false && $tasks[$k]['cmd'] != false) {
            $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
            $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
        }
        
    }
    foreach ($death_list as $d){
        unset($tasks[$d]);
    }
    sort($tasks);

    $death_list = array();

    //On charge les taches manuelles à faire
    $tasks_man = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']), "AND", array("done", "=", 0)));
    foreach ($tasks_man as $k => $t){
        //On récupère la commande correspondante
        $tasks_man[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));
        if ($tasks_man[$k]['cmd']['is_manual'] == false){
            array_push($death_list, $k);
        }
    }
    foreach ($death_list as $d){
        unset($tasks_man[$d]);
    }
    sort($tasks_man);


    $tasks_done = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id']), "AND", array("done", "=", 1)));

    foreach ($tasks_done as $k => $t){
        //On récupère la commande correspondante
        $tasks_done[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd']))); 
        if (!defined("DServerLink") || !DServerLink || $tasks_done[$k]['cmd']['is_manual'] == true || $tasks_done[$k]['cmd']['is_manual'] == '1'){
            $tasks_done[$k]['cmd']['server_name'] = "";
            $tasks_done[$k]['cmd']['server_game'] = "";
        }else if ($tasks_done[$k]['cmd']['is_manual'] == false && $tasks_done[$k]['cmd'] != false) {
            $tasks_done[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks_done[$k]['cmd']['server']]['name'];
            $tasks_done[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks_done[$k]['cmd']['server']]['game'];
        }
    }
    //var_dump($tasks, $commande, $tasks_done, $tasks_man);
    $controleur_def->loadView('admin/boutique/iframe.getback', 'accueil', "IFRAME Etat de la commande " . $uuid);
    die;

//Si on charge le gestionnaire d'articles
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "articles"){
    if (isset($_POST['name']) && !empty($_POST['name']) &&
        isset($_POST['desc']) && !empty($_POST['desc']) &&
        isset($_POST['prix']) &&
        isset($_POST['cat']) && !empty($_POST['cat']) &&
        isset($_POST['nb_auto_tasks']) &&
        isset($_POST['nb_man_tasks']) ){
    
        //Le formulaire de "base" est complet, analysons si une image a bien été envoyée, et étudions la config pour le lien avec les serveurs
        if (isset($_FILES['img']) && $_FILES['img']['size'] != 0){
            //S'il s'agit bien d'une image
            if (strrpos($_FILES['img']['type'], "image/") === false || (substr($_FILES['img']['name'], -3) != "jpg" && substr($_FILES['img']['name'], -3) != "png" && substr($_FILES['img']['name'], -4) != "jepg")){
                $controleur_def->addError(524);
            }else {
                $upload = uploadFile('img', "boutique");
                //S'il y a une erreur dans l'upload
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
                                //Depuis la 1.1 : Nouveau système avec les commandes manuelles
                                $nb_man_tasks = intval($_POST['nb_man_tasks']);
                                $nb_auto_tasks = intval($_POST['nb_auto_tasks']);
                                //On commence par les tâches manuelles
                                if ($nb_man_tasks > 0){
                                    for ($i = 0; $i < $nb_man_tasks; $i++){
                                        if (isset($_POST['man_cmd'][$i]) && !empty($_POST['man_cmd'][$i])){
                                            if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_cmd",
                                                    array("cmd", "connexion_needed", "id_article", "is_manual"),
                                                    array($_POST['man_cmd'][$i], false, $id['id'], 1))){
                                                        $controleur_def->addError("342c");                                            
                                            }
                                        }
                                    }
                                }

                                //On traite ensuite les tâches automatiques en vérifiant d'abord si DSL est bien activé
                                if (defined("DServerLink") && DServerLink == true && $nb_auto_tasks > 0){
                                    $serveurs = $cm->getConfig();
                                    for ($i = 0; $i < $nb_auto_tasks; $i++){
                                        if (isset($_POST['cmd'][$i]) && !empty($_POST['cmd'][$i]) &&
                                        isset($_POST['server'][$i]) && !empty($_POST['server'][$i])){
                                            //On commence par vérifier que le serveur demandé existe et est bien activé
                                            if (isset($serveurs[intval($_POST['server'][$i])]) && $serveurs[intval($_POST['server'][$i])]['enabled']){
                                                //Si c'est le cas, on regarde si on doit obliger le joueur à être connecté pour recevoir son dû
                                                $mustbeconnected = false;
                                                if (is_array($_POST['mustbe_connected']) && isset($_POST['mustbe_connected'][$i]) && !empty($_POST['mustbe_connected'][$i]) && $_POST['mustbe_connected'][$i] == "on"){
                                                    $mustbeconnected = true;
                                                }
                                                //On enregistre donc bien la commande
                                                if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_cmd",
                                                array("cmd", "connexion_needed", "server", "id_article", "is_manual"),
                                                array($_POST['cmd'][$i], $mustbeconnected, $_POST['server'][$i], $id['id'], 0))){
                                                    $controleur_def->addError("342c");                                            
                                                }
                                            }else {
                                                $controleur_def->addError(480);
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
            $cats[$key]['articles'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_articles" ,"*", array(array("cat", "=", $cats[$key]['id']), "AND", array("archive", "=", 0)));
            foreach ($cats[$key]['articles'] as $k => $c){
                if (strpos($cats[$key]['articles'][$k]['img'], "png") !== false) {
                    $cats[$key]['articles'][$k]['link'] = LINK . 'getimage/png/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/200/200';
                }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpg") !== false) {
                    $cats[$key]['articles'][$k]['link'] =  LINK . 'getimage/jpg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/200/200';
                }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpeg") !== false) { 
                    $cats[$key]['articles'][$k]['link'] = LINK . 'getimage/jpeg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -5) . '/200/200';
                }
                //On récupère les commandes associées
                $cats[$key]['articles'][$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cmd", "*", array(array("id_article", "=", $cats[$key]['articles'][$k]['id']), "AND", array("archive", "=", 0)));
                //On récupère le nombre de ventes
                $commandes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*", array(array("id_article", "=", $cats[$key]['articles'][$k]['id'])));
                if ($commandes == false || empty($commandes)){
                    $cats[$key]['articles'][$k]['ventes'] = 0;
                }else {
                    $cats[$key]['articles'][$k]['ventes'] = sizeof($commandes);
                }
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
        //Si on cherche à suspendre une tache
        if (isset($param[3]) && $param[3] == "xhr" && isset($param[4]) && $param[4] == "stop" && isset($param[5])){
            define('FORCE_INLINE_ERR', true);
            //On commence par essayer de mieux connaitre la tache
            $task = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_todolist", "*", array( array( "id", "=", intval($param[5]) ) ));
            if (empty($task)){
                die('Error : Impossible de trouver la tâche (Erreur critique, contacter le support)');
            }
            if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_todolist", 
                array(
                    array("done", "=", 1), 
                    array("date_done", "=", date("Y-m-d H:i:s")),
                    array("stopped", "=", 1),
                    array("stopped_reason", "=", "Tâche interrompue par un administrateur (" . $_SESSION['user']->getPseudo() . ").")
                ), array(array("id", "=", $param[5])))){
                    die('Error');
                }
            //Mais, attention, si c'est la dernière tâche, on doit clore la commande
            $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $task['id_commande']), "AND", array("done", "=", 0)));
            if (empty($tasks)){
                if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_achats", 
                    array(
                        array("success", "=", 1)
                    ), array(array("id", "=", $task['id_commande'])))){
                        die('Error: Impossible de clore la commande');
                }
            }
            die('Success');
        }

        //Si on cherche à terminer une tache manuelle
        if (isset($param[3]) && $param[3] == "xhr" && isset($param[4]) && $param[4] == "done" && isset($param[5])){
            define('FORCE_INLINE_ERR', true);

            //On commence par essayer de mieux connaitre la tache
            $task = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_todolist", "*", array( array( "id", "=", intval($param[5]) ) ));
            if (empty($task)){
                die('Error : Impossible de trouver la tâche (Erreur critique, contacter le support)');
            }
            if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_todolist", 
                array(
                    array("done", "=", 1), 
                    array("date_done", "=", date("Y-m-d H:i:s")),
                ), array(array("id", "=", $param[5])))){
                    die('Error');
                }
            //Mais, attention, si c'est la dernière tâche, on doit clore la commande
            $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $task['id_commande']), "AND", array("done", "=", 0)));
            if (empty($tasks)){
                if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_achats", 
                    array(
                        array("success", "=", 1)
                    ), array(array("id", "=", $task['id_commande'])))){
                        die('Error: Impossible de clore la commande');
                }
            }
            die('Success');
        }

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
            //On récupère les taches correspondantes
            $tasks[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd']))); 
            
            if (!defined("DServerLink") || !DServerLink){
                $tasks[$k]['cmd']['server_name'] = "";
                $tasks[$k]['cmd']['server_game'] = "";
            }else if ($tasks[$k]['cmd']['is_manual'] == false && $tasks[$k]['cmd'] != false) {
                $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
                $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
            }

            //On récupère la commande
            $tasks[$k]['commande'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_achats", "*", array(array("id", "=", $t['id_commande'])));
            
        }        
        $config = $Serveur_Config;
        $controleur_def->loadJS('admin/boutique/tasks');
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