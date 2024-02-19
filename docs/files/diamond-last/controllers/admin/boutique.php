<?php 

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);

//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $adminBuilder = $tb->AdminBuilder("Vous n'avez pas l'autorisation d'accéder à ces réglages", "Veuillez contacter un administrateur pour obtenir un grade plus élevé.");
    echo $adminBuilder->render();
    die;
}

$boutique_config = cleanIniTypes(parse_ini_file(ROOT . 'config/boutique.ini', true));

if (isset($param[2]) && $param[2] != "config" && !$Serveur_Config['en_boutique']){
    $adminBuilder = $tb->AdminBuilder("Boutique désactivée", "Veuillez activer préalablement la boutique par défaut pour accéder à ces informations.");
    echo $adminBuilder->render();
    die;
}

if (isset($param[2]) && !empty($param[2]) && $param[2] == "articles"){
        if (defined("DServerLink") && DServerLink == true){
            $serveurs = $cm->getConfig();
        }
        $cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cat" ,"*");
        foreach ($cats as $key => $cat){
            $cats[$key]['articles'] = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_articles" ,"*", array(array("cat", "=", $cats[$key]['id']), "AND", array("archive", "=", 0))));

            foreach ($cats[$key]['articles'] as $k => $c){
                if (strpos($cats[$key]['articles'][$k]['img'], "png") !== false) {
                    $cats[$key]['articles'][$k]['link'] = LINK . 'getimage/png/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/500/500';
                }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpg") !== false) {
                    $cats[$key]['articles'][$k]['link'] =  LINK . 'getimage/jpg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/500/500';
                }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpeg") !== false) { 
                    $cats[$key]['articles'][$k]['link'] = LINK . 'getimage/jpeg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -5) . '/500/500';
                }else { 
                    $cats[$key]['articles'][$k]['link'] = LINK . 'getprofileimg/noprofile/500';
                }
                //On récupère les commandes associées
                $cats[$key]['articles'][$k]['cmd'] = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cmd", "*", array(array("id_article", "=", $cats[$key]['articles'][$k]['id']), "AND", array("archive", "=", 0))));
                //On récupère le nombre de ventes
                $commandes = cleanIniTypes(simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*", array(array("id_article", "=", $cats[$key]['articles'][$k]['id']))));
                if ($commandes == false || empty($commandes)){
                    $cats[$key]['articles'][$k]['ventes'] = 0;
                }else {
                    $cats[$key]['articles'][$k]['ventes'] = sizeof($commandes);
                }
            }
        }               
        $config = $Serveur_Config;
        $controleur_def->loadJS('admin/boutique.articles');
        $controleur_def->loadViewAdmin('admin/boutique.articles', 'accueil', "Gestion des articles");
        die;
//Si on charge les tâches et commandes à satisfaire
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "tasks"){    
        if (defined("DServerLink") && DServerLink == true){
            $serveurs = $cm->getConfig();
        }

        //On récupère toutes les dernières tâches
        $tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", false, 'id', true);
        
        //On retire toutes les tâches déjà terminées à partir du 20e rang
        $it = 0;
        foreach($tasks as $key => $t){
            $it++;
            if ($it > 20 && ($t['done'] == '1' || $t['stopped'] == '1'))
                unset($tasks[$key]);
        }

        //On récupère toutes les dernières commandes
        $commandes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*", false, 'id', true);
        //On retire toutes les commandes déjà terminées à partir du 20e rang
        $it = 0;
        foreach($commandes as $key => $c){
            $it++;
            if ($it > 20 && ($c['success'] == '1'))
                unset($commandes[$key]);
        }
        
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
            
            if ((!defined("DServerLink") || !DServerLink) && is_array($tasks[$k]['cmd'])){
                if ($tasks[$k]['cmd']['is_manual'] == false && $tasks[$k]['cmd']['server'] == -1){
                    $tasks[$k]['cmd']['server_name'] = "Site internet";
                    $tasks[$k]['cmd']['server_game'] = "API Web";
                }else {
                    $tasks[$k]['cmd']['server_name'] = "";
                    $tasks[$k]['cmd']['server_game'] = "";
                }
            }else if (is_array($tasks[$k]['cmd']) && $tasks[$k]['cmd']['is_manual'] == false && $tasks[$k]['cmd'] != false) {
                if ($tasks[$k]['cmd']['is_manual'] == false && $tasks[$k]['cmd']['server'] == -1){
                    $tasks[$k]['cmd']['server_name'] = "Site internet";
                    $tasks[$k]['cmd']['server_game'] = "API Web";
                }else {
                    $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
                    $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
                }
            }

            //On récupère la commande
            $tasks[$k]['commande'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_achats", "*", array(array("id", "=", $t['id_commande'])));
            
        }     
        
        $config = $Serveur_Config;
        //$controleur_def->loadJS('admin/boutique/tasks');
        //$controleur_def->loadViewAdmin('admin/boutique/tasks', 'accueil', "Tâches et achats virtuels");


        $adminBuilder = $tb->AdminBuilder("Boutique - Tâches et commandes récentes", "DiamondCMS est livré avec une boutique : sur celle-ci les utilisateurs peuvent acheter vos articles avec un monnaie virtuelle. Une tâche est une action à exécuter pour que l'acheteur reçoive son dû. (exemple: une commande sur un serveur).<br><strong>Pour accèder à la documentation : <a href=\"https://github.com/Aldric-L/DiamondCMS/wiki/Boutique\">Cliquez-ici</a></strong> ");
        
        $mt = $controleur_def->getManualTasks();
        if (sizeof($mt) != 0){
            //$adminBuilder->addColumn($tb->UIColumn("col-lg-12", array($tb->AdminCard("bg-danger", "<strong>Attention !</strong> Des tâches manuelles attendent d'être exécutées, des clients ne peuvent récupérer leurs achats !", ""), $tb->UIString("<br>"))));
             $adminBuilder->addAlert("lg-12", $tb->AdminAlert("danger", "Attention !", "Des tâches manuelles attendent d'être exécutées, des clients ne peuvent récupérer leurs achats !", true));

        }

        if (is_array($commandes) && !empty($commandes)){
            //On commence par générer la liste des commandes
            $list = $tb->AdminList();
            foreach ($commandes as $c){
                //Chaque commande ouvre droit sur un modal qu'on écrit
                $adminBuilder->addModal(
                    $modal = $tb->AdminModal("Aperçu du reçu client de la commande (". $c['uuid'] .") de " . $c['user'], "recu_" . $c['uuid'], 
                                            $tb->UIIframe(LINK . "boutique/getback/" . $c['uuid'] . "/ADMIN-IFRAME")->setDiv("embed-responsive embed-responsive-4by3"), "", "modal-lg"));

                $status = ($c['success'] != true && $c['success'] != 1 ) ? '<strong><span style="color: red;">En cours</span></strong>' : '<strong><span style="color: green;">Terminée !</span></strong>';
                $list->addField(
                    $tb->UIString("<strong>N°" . $c['uuid']."</strong> : le ". $c['date'] . " par " . $c['user'] . " (Article : " . $c['article'] . ", " . $c['price'] . " " . $config['Serveur_money'] . "s)"), 
                    $tb->UIString($status), $modal);
            }
        }else {
            $list = $tb->UIString("<p><em>Il n'y a aucune commmande enregistrée pour l'instant.</em><p>");
        }

        
        //La page est constituée d'un panel principal qu'on écrit
        $panel1 = $tb->AdminPanel("Dernières commandes enregistrées", "fa-shopping-bag", $list, "lg-6");
        $adminBuilder->addPanel($panel1);


        if (is_array($tasks) && !empty($tasks)){
            //On commence par générer la liste des commandes
            $list = $tb->AdminList();
            foreach ($tasks as $t){
                $alert = '<span style="color: red;!important"><i style="color: red;!important" class="fa fa-warning fa-fw" aria-hidden="true"></i> </span>';

                //Chaque commande ouvre droit sur un modal qu'on écrit
                if ($t['done'] != 'true' && $t['done'] != '1' )
                    $status = '<strong>Statut : <span style="color: orange;">En cours</span></strong>';
                else if($t['stopped'] == '1')
                    $status = '<strong>Statut : <span style="color: red;">Suspendue</span></strong>';
                else 
                    $status = '<strong>Statut : <span style="color: green;">Terminée</span></strong>';


                $infotext = "
                <p>Cette tâche est associée à la <a href=\"#\" data-dismiss=\"modal\" data-toggle=\"modal\" data-target=\"#recu_" . $t['commande']['uuid'] ."\">commande n°". $t['commande']['uuid'] ." (de ".  $controleur_def->getPseudo($t['commande']['id_user']) ."</a>).<br>
                <strong>Elle consiste en \"<em>" . ((is_array($t['cmd'])) ? $t['cmd']['cmd'] : "Inconnu") . "</em>\".</strong><br>" . $status .
                "<br>Elle a été initiée le " . $t['date_send'] . "
                <br></p>
                ";
                if ($t['stopped'] == '1'){
                    $infotext .= "<p><strong>Motif d'interruption : </strong>" . ((isset($t['stopped_reason']) ? $t['stopped_reason'] : "Inconnue"));
                    $infotext .= "<br><strong>Date d'achèvement : </strong>" . ((isset($t['date_done']) ? $t['date_done'] : "Inconnue")) . "<br></p>";
                }else if ($t['stopped'] == '1' || ($t['done'] == 'true' || $t['done'] == '1')){
                    $infotext .= "<p><strong>Date d'achèvement : </strong>" . ((isset($t['date_done']) ? $t['date_done'] : "Inconnue")) . "<br></p>";
                }
                if (is_array($t) && is_array($t['cmd']) && $t['cmd']['is_manual'] != '1'){
                    $type = "Automatique";
                    $infotext .= "<p><em>Cette tâche est une tâche automatique et ne nécessite pas votre intervention. Le joueur peut l'exécuter quand il le souhaite.</em><br>";
                    $infotext .= "<strong>Serveur concerné :</strong> " . $t['cmd']['server_name'] . " (" . $t['cmd']['server_game'] . ") <br>";
                    $infotext .= "<strong>Le joueur doit-il être connecté :</strong> " . (($t['cmd']['connexion_needed'] == '1') ? "Oui" : "Non") . "<br>";
                }
                else if (is_array($t) && is_array($t['cmd']) && $t['cmd']['is_manual'] == '1'){
                    $type = "Manuelle";
                    if ($t['done'] != 'true' && $t['done'] != '1')
                        $infotext .= '<strong class="text-danger">';
                    $infotext .= "<p class=\"text-justify\"><em>Cette tâche est une tâche manuelle. Elle nécessite donc l'intervention d'un administrateur. Le joueur, qui a acheté un article, ne peut pas récupérer son dû tant qu'un administrateur n'a pas effectué les tâches manuelles associées à sa commande.</em></p>";
                    if ($t['done'] != 'true' && $t['done'] != '1')
                        $infotext .= "</strong>";
                }
                else {
                    $type = "Inconnu";
                    $infotext .= "<p class=\"text-danger\"><em>Une erreur grave est survenue. Nous vous invitons à rembourser ou du moins examiner finement la commande concernée car une tâche a disparu du système.</em><br>";
                }
                
                $adminBuilder->addModal(
                    $modal = $tb->AdminModal("Information sur la tâche n°" . $t['id'], "taskinfo_" . $t['id'],
                                            $tb->UIString($infotext), ""));
                
                $del_task_btn = $tb->AdminAPIButton("Suspendre", "btn-danger btn-sm", LINK . "api/", "boutique", "set", "stopTask", "id_task=" . (string)$t['id'], "", "true", "false", "false", ($t['stopped'] == '1' || $t['done'] == '1'));
                $complete_task_btn = $tb->AdminAPIButton("Terminer", "btn-success btn-sm", LINK . "api/", "boutique", "set", "completeTask", "id_task=" . (string)$t['id'], "", "true", "false", "false", ($t['stopped'] == '1' || $t['done'] == '1' || (is_array($t['cmd']) && $t['cmd']['is_manual'] != '1')));
                $modal->addAPIButton($tb->UIArray(array($complete_task_btn, $del_task_btn)));
                
                $list->addField(
                    $tb->UIString(
                        (($t['done'] != 'true' && $t['done'] != '1' && is_array($t) && is_array($t['cmd'])  && $t['cmd']['is_manual']) ? $alert : "") .
                        "<strong>N°" . $t['id']." " . $type . "</strong> - " . $status), 
                    $tb->UIArray(array($complete_task_btn, $del_task_btn)), $modal);
            }
        }else {
            $list = $tb->UIString("<p><em>Il n'y a aucune tâche enregistrée pour l'instant.</em><p>");
        }
        //La page est constituée d'un panel secondaire qu'on écrit
        $panel2 = $tb->AdminPanel("Dernières tâches enregistrées ou restant à effectuer", "fa-truck", $list, "lg-6");
        $adminBuilder->addPanel($panel2);
//Si on charge le gestionnaire de PayPal
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "paypal"){
    $adminBuilder = $tb->AdminBuilder("Boutique - Gestion de PayPal", "La boutique de DiamondCMS repose sur une monnaie virtuelle que les joueurs peuvent acheter. Pour cela, vous pouvez paramètrer des offres PayPal (plus rentable pour vous que DediPass par exemple).<br><strong>Pour accèder à la documentation : <a href=\"https://github.com/Aldric-L/DiamondCMS/wiki/Boutique\">Cliquez-ici</a></strong> ");

    // Le panel des réglages divers
    $options = $tb->AdminForm("paypal-options", false)
    ->addCheckField("en_paypal", "Activer le paiement par PayPal", $boutique_config['PayPal']['en_paypal'])
    ->addCheckField("sandbox", "Activer le mode sandbox", $boutique_config['PayPal']['sandbox'])
    ->addTextField("money", "Monnaie utilisée", $boutique_config['PayPal']['money'])
    ->addTextField("id", "ID PayPal", ($boutique_config['PayPal']['id'] == null) ? "" : $boutique_config['PayPal']['id'])
    ->addTextField("secret", "Secret PayPal", ($boutique_config['PayPal']['secret'] == null) ? "" : $boutique_config['PayPal']['secret']);
    $options->addAPIButton($tb->AdminAPIButton("Sauvegarder", "btn btn-custom", LINK . "api/", "boutique", "set", "PayPalConfig", $options , "", true ))
    ->setButtonsLine('class="text-right"');

    $panel_gen = $tb->AdminPanel("Réglages PayPal", "fa-wrench", $options, "lg-3");
    

    // Le Panel de l'ajout d'offres PayPal
    if ($boutique_config['PayPal']['en_paypal']){
        $new_offer = $tb->AdminForm("boutique-addOffer", true)
        ->addTextField("name", "Titre de la nouvelle offre")
        ->addNumberField("price", "Prix en " . $Serveur_Config['money_name'] . "s")
        ->addNumberField("tokens", "Nombre de " . $Serveur_Config['Serveur_money'] . "s en échange");
        $new_offer->addAPIButton($tb->AdminAPIButton("Ajouter", "btn btn-custom", LINK . "api/", "boutique", "set", "addPayPalOffer", $new_offer , "", true ))
        ->setButtonsLine('class="text-right"');
        
        $panel_addOffer = $tb->AdminPanel("Ajouter une offre PayPal", "fa-cart-plus",$new_offer, "lg-3");
    }else if(!$boutique_config['PayPal']['en_paypal']){
        $panel_addOffer = $tb->AdminPanel("Ajouter une offre PayPal", "fa-cart-plus",$tb->UIString('<p class="text-center"><em>PayPal est désactivé.</em></p>'), "lg-3");
    }

    // Le Panel des offres enregistrées
    $offres = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_paypal_offres", "*", false, "id", true);
    if (!empty($offres) && $offres != false && $boutique_config['PayPal']['en_paypal']){
        $table_offers = $tb->AdminTable("table-striped", "boutique-OffersTable", array("Id", "Nom", "Prix réel", "Valeur virtuelle", "UUID", "Action"));
    
        foreach($offres as $o){
            $table_offers->addLine(array("Id" => $o['id'], "Nom" => $o['name'], "Prix réel" => $o['price'] . $Serveur_Config['money'], 
            "Valeur virtuelle" => $o['tokens'], "UUID" => $o['uuid'], 
            "Action" => $tb->AdminAPIButton("Supprimer", "btn btn-sm btn-danger", LINK . "api/", "boutique", "set", "delPayPalOffer", "id=" . $o['id'] , "", true )));
        }
        
        $panel_Offers = $tb->AdminPanel("Offres enregistrées", "fa-shopping-cart",$table_offers, "lg-9");
    }else if(!$boutique_config['PayPal']['en_paypal']){
        $panel_Offers = $tb->AdminPanel("Offres enregistrées", "fa-shopping-cart",$tb->UIString('<p class="text-center"><em>PayPal est désactivé.</em></p>'), "lg-9");
    }
    else {
        $panel_Offers = $tb->AdminPanel("Offres enregistrées", "fa-shopping-cart",$tb->UIString('<p class="text-center"><em>Aucune offre n\'a pour l\'instant été enregistrée.</em></p>'), "lg-9");
    }


    // Le Panel des paiements enregistrés
    $payments = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_paypal", "*", false, "id", true);
    
    if (!empty($payments) && $payments != false){
        $table_payments = $tb->AdminTable("table-striped", "boutique-PayPalPaymentsTable", array("Id PayPal", "Membre", "Email Paypal", "Prix", "Date", $Serveur_Config['Serveur_money'] . "s"));

        foreach($payments as $p){
            $table_payments->addLine(array("Id PayPal" => $p['payment_id'], "Membre" => $controleur_def->getPseudo($p['user']), 
            "Email Paypal" => ($p['payment_status'] == "created") ? '<span style="color: red;">Paiement inachevé</span>' : $p['payer_email'], 
            "Prix" => $p['payment_amount'], "Date" => $p['payment_date'], 
            $Serveur_Config['Serveur_money'] . "s" => $p['money_get']));
        }

        $panel_payments = $tb->AdminPanel("Paiements enregistrées", "fa-credit-card-alt",$table_payments, "lg-9");
    }else {
        $panel_payments = $tb->AdminPanel("Paiements enregistrées", "fa-credit-card-alt",$tb->UIString('<p class="text-center"><em>Aucun paiement n\'a pour l\'instant été enregistré.</em></p>'), "lg-9");
    }
    
    $adminBuilder->addColumn($tb->UIColumn("col-lg-3", array($panel_gen, $panel_addOffer)));
    $adminBuilder->addColumn($tb->UIColumn("col-lg-9", array($panel_Offers, $panel_payments)));

//Si on charge le gestionnaire DediPass
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "dedipass"){
    $adminBuilder = $tb->AdminBuilder("Boutique - Gestion de DediPass", "La boutique de DiamondCMS repose sur une monnaie virtuelle que les joueurs peuvent acheter. Pour cela, vous pouvez paramètrer le service de paiement DediPass. <em>Contrairement à Paypal, les offres (combien de monnaie virtuelle contre combien de monnaie réelle) sont à paramétrer directement sur le site de Dedipass, ici on se contente de relier DediPass à DiamondCMS.</em><br><strong>Pour accèder à la documentation : <a href=\"https://github.com/Aldric-L/DiamondCMS/wiki/Boutique\">Cliquez-ici</a></strong> ");

    // Le panel des réglages divers
    $options = $tb->AdminForm("dedidpass-options", false)
    ->addCheckField("en_ddp", "Activer le paiement par DediPass", $boutique_config['DediPass']['en_ddp'])
    ->addTextField("public_key", "Clée publique", ($boutique_config['DediPass']['public_key'] == null) ? "" : $boutique_config['DediPass']['public_key'])
    ->addTextField("private_key", "Clée privée", ($boutique_config['DediPass']['private_key'] == null) ? "" : $boutique_config['DediPass']['private_key']);
    $options->addAPIButton($tb->AdminAPIButton("Sauvegarder", "btn btn-custom", LINK . "api/", "boutique", "set", "DDPConfig", $options , "", true ))
    ->setButtonsLine('class="text-right"');

    $panel_gen = $tb->AdminPanel("Réglages DediPass", "fa-wrench", $options, "lg-3");

    
    // Le Panel des paiements enregistrés
    $payments = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_dedipass", "*", false, "id", true);
    
    if (!empty($payments) && $payments != false){
        $table_payments = $tb->AdminTable("table-striped", "boutique-DediPassPaymentTable", 
        array("Id", "Membre", "Code utilisé", "Prix", "Date", $Serveur_Config['Serveur_money'] . "s"));

        foreach($payments as $p){
            $table_payments->addLine(array("Id" => $p['id'], "Membre" => $controleur_def->getPseudo($p['id_user']), 
            "Code utilisé" => $p['code'], 
            "Prix" => $p['payout'], "Date" => $p['date'], 
            $Serveur_Config['Serveur_money'] . "s" => $p['virtual_currency']));
        }

        $panel_payments = $tb->AdminPanel("Paiements enregistrées", "fa-credit-card-alt",$table_payments, "lg-9");
    }else {
        $panel_payments = $tb->AdminPanel("Paiements enregistrées", "fa-credit-card-alt",$tb->UIString('<p class="text-center"><em>Aucun paiement n\'a pour l\'instant été enregistré.</em></p>'), "lg-9");
    }

    $adminBuilder->addPanel($panel_gen);
    $adminBuilder->addPanel($panel_payments);

//Si on charge les statistiques
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "statistiques"){
    $adminBuilder = $tb->AdminBuilder("Boutique - Statistiques", "La boutique de DiamondCMS vous permet de suivre vos revenus générés par les paiements effectués par PayPal ou Dédipass par vos clients. ");

    $payments_dedipass = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_dedipass", "*", false, "id", true);
    $payments_paypal = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_paypal", "*", array(array("payment_status", "=", "approved")), "id", true);
    
    function getPaymentsBy($payments_paypal, $payments_dedipass, $format){
        $payments_by_day = array();
        foreach ($payments_paypal as $p){
            if (isset($p["payment_date"]) && !empty($p["payment_date"])){
                $date = new DateTime($p["payment_date"]);
                $day = $date->format($format);
                if (array_key_exists($day, $payments_by_day)){
                    $payments_by_day[$day] = array(
                        "nb_pp_payments" => $payments_by_day[$day]["nb_pp_payments"]+1,
                        "value_pp_payments" => $payments_by_day[$day]["value_pp_payments"]+intval($p["payment_amount"]),
                        "nb_dp_payments" => $payments_by_day[$day]["nb_dp_payments"],
                        "value_dp_payments" => $payments_by_day[$day]["value_dp_payments"],
                        "date" => $payments_by_day[$day]["date"],
                    );
                }else {
                    $payments_by_day[$day] = array(
                        "date" => $date->format($format),
                        "nb_pp_payments" => 1,
                        "value_pp_payments" => intval($p["payment_amount"]),
                        "nb_dp_payments" => 0,
                        "value_dp_payments" => 0,
                    );
                }
            }
        }
        foreach ($payments_dedipass as $p){
            if (isset($p["date"]) && !empty($p["date"])){
                $date = new DateTime($p["date"]);
                $day = $date->format($format);
                if (array_key_exists($day, $payments_by_day)){
                    $payments_by_day[$day] = array(
                        "nb_dp_payments" => $payments_by_day[$day]["nb_dp_payments"]+1,
                        "value_dp_payments" => $payments_by_day[$day]["value_dp_payments"]+intval($p["payout"]),
                        "nb_pp_payments" => $payments_by_day[$day]["nb_pp_payments"],
                        "value_pp_payments" => $payments_by_day[$day]["value_pp_payments"],
                        "date" => $payments_by_day[$day]["date"],
                    );
                }else {
                    $payments_by_day[$day] = array(
                        "date" => $date->format($format),
                        "nb_pp_payments" => 0,
                        "value_pp_payments" => 0,
                        "nb_dp_payments" => 1,
                        "value_dp_payments" => intval($p["payout"]),
                    );
                }
            }
        }
        return $payments_by_day;
    }
    $payments_by_day = getPaymentsBy($payments_paypal, $payments_dedipass, "d/m/Y");
    $payments_by_month = getPaymentsBy($payments_paypal, $payments_dedipass, "m/Y");
    $payments_by_year = getPaymentsBy($payments_paypal, $payments_dedipass, "Y");
    if (!empty($payments_by_day)){
        $payments_sorted= $payments_by_day;
        if (sizeof($payments_by_day) > 32)
            $payments_sorted= $payments_by_month;
        sort($payments_sorted);
        $labels = array();
        $nb_pp_payments = array();
        $value_pp_payments = array();
        $nb_dp_payments = array();
        $value_dp_payments = array();
        foreach ($payments_sorted as $l => $p){
            array_push($labels, $payments_sorted[$l]["date"]);
            array_push($nb_pp_payments, $payments_sorted[$l]["nb_pp_payments"]);
            array_push($value_pp_payments, $payments_sorted[$l]["value_pp_payments"]);
            array_push($nb_dp_payments, $payments_sorted[$l]["nb_dp_payments"]);
            array_push($value_dp_payments, $payments_sorted[$l]["value_dp_payments"]);
        }
        $areachart = $tb->AdminAreaChart($adminBuilder, array("labels" => $labels, "datasets" => array($value_pp_payments, $value_dp_payments)), array("y_label" => array("Valeur ventes PayPal", "Valeur ventes DediPass")), "hitschart");
    }else {
        $areachart = $tb->UINull();
    }
    
    $monthlyearncard = $tb->AdminCard("custom", "Chiffre d'affaires (Mensuel)", 
    (array_key_exists(date('m/Y'), $payments_by_month) ? $payments_by_month[date('m/Y')]["value_pp_payments"]+$payments_by_month[date('m/Y')]["value_dp_payments"] : "0") . $Serveur_Config["money"]);
    $annppearncard = $tb->AdminCard("custom", "Chiffre d'affaires PayPal (Annuel)", 
    (array_key_exists(date('Y'), $payments_by_year) ? $payments_by_year[date('Y')]["value_pp_payments"] : "0") . $Serveur_Config["money"]);
    $anndpearncard = $tb->AdminCard("custom", "Chiffre d'affaires DediPass (Annuel)", 
    (array_key_exists(date('Y'), $payments_by_year) ? $payments_by_year[date('Y')]["value_dp_payments"] : "0"). $Serveur_Config["money"]);
    $leftcolumn = $tb->UIColumn("lg-2", array($monthlyearncard, $annppearncard, $anndpearncard));


    $noStats = $tb->UIString("<p class=\"text-center\"><em>Aucun achat enregistré pour l'instant.</em></p>");
    
    $clicpanel = $tb->AdminPanel("Chiffre d'affaires par mode de paiement", "fa-euro", (empty($payments_by_day) ? $noStats : $areachart), "lg-10");

    $adminBuilder->addColumn($leftcolumn);
    $adminBuilder->addPanel($clicpanel);

    $dpvsppchart = $tb->AdminPieChart($adminBuilder, array("labels" => array("DediPass", "PayPal"), 
    "data" => array(
        (array_key_exists(date('Y'), $payments_by_year) ? intval($payments_by_year[date('Y')]["value_dp_payments"]) : 0),
        (array_key_exists(date('Y'), $payments_by_year) ? intval($payments_by_year[date('Y')]["value_pp_payments"]) : 0),
    )), "pageschart", true);
    $dpvspppanel = $tb->AdminPanel("Chiffre d'affaires par mode de paiement", "fa-shopping-cart", $dpvsppchart, "lg-6");

    $adminBuilder->addPanel($dpvspppanel);

    $buys = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_achats", "*", false, "id_article");
    $articles = array();
    foreach ($buys as $b){
        if (array_key_exists($b["id_article"], $articles)){
            $articles[$b["id_article"]] = $articles[$b["id_article"]]+1;
        }else {
            $articles[$b["id_article"]] = 1;
        }
    }
    $labels_articles = array();
    $sales_articles = array();
    $articles_select = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_articles", "*", false);
    foreach ($articles_select as $a){
        if (array_key_exists($a['id'], $articles)){
            array_push($labels_articles, $a['name']);
            array_push($sales_articles, $articles[$a['id']]);
        }
    }
    
    $articleschart = $tb->AdminPieChart($adminBuilder, array("labels" => $labels_articles, "data" => $sales_articles), "articleschart", true);
    $articlespanel = $tb->AdminPanel("Meilleures ventes", "fa-star", $articleschart, "lg-6");
    $adminBuilder->addPanel($articlespanel);

}else {
    $adminBuilder = $tb->AdminBuilder("Boutique - Configuration générale", "DiamondCMS est livré avec une boutique qu'il convient de paramètrer finement pour pouvoir dégager, de manière sécurisée, des revenus de vos serveurs.<br><strong>Pour accèder à la documentation : <a href=\"https://github.com/Aldric-L/DiamondCMS/wiki/Boutique\">Cliquez-ici</a></strong> ");
    
    // Le panel des réglages divers
    $options = $tb->AdminForm("boutique-options", true)
    ->addTextField("money_name", "Nom de la monnaie réelle de paiement", $Serveur_Config['money_name'], true, !$Serveur_Config['en_boutique'])
    ->addTextField("money_sym", "Symbole de la monnaie réelle", $Serveur_Config['money'], true, !$Serveur_Config['en_boutique'])
    ->addTextField("money", "Nom de la monnaie virtuelle", $Serveur_Config['Serveur_money'], true, !$Serveur_Config['en_boutique']);
    $options->addAPIButton($tb->AdminAPIButton("Sauvegarder", "btn btn-custom", LINK . "api/", "boutique", "set", "genConfig", $options , "", true ))
    ->setButtonsLine('class="text-right"');
    
    $panel_gen = $tb->AdminPanel("Réglages généraux de la boutique par défaut de DiamondCMS", "fa-wrench", $options, "lg-6");
    
    // Le panel des catégories
    $cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cat" ,"*");
    if (is_array($cats) && !empty($cats) && $Serveur_Config['en_boutique']){
        $list = $tb->AdminList();
        foreach ($cats as $c){    
            $del_btn = $tb->AdminAPIButton("Supprimer", "btn btn-sm btn-danger", LINK . "api/", "boutique", "set", "delCategory", "id=" . $c['id'], "", true);   
            $list->addField($tb->UIString("<strong>". $c['name'] ."</strong> (" . $c['nb_articles']. " article(s) enregistré(s) à l'intérieur)"), $del_btn);
        }
    }else if ($Serveur_Config['en_boutique']){
        $list = $tb->UIString("<p><em>Aucune catégorie n'est pour le moment enregistrée.</em><p>");
    }else {
        $list = $tb->UIString("<p><em>La boutique par défaut est désactivée</em><p>");
    }
    $panel_cat = $tb->AdminPanel("Catégories d'articles enregistrées", "fa-shopping-bag", $list, "lg-6");
    
    
    // Le panel de la boutique externe
    $disablePreText = $tb->UiString("<p class=\"text-justify\"><em><strong>Nous ne sommes pas jaloux !</strong> DiamondCMS vous permet d'utiliser une boutique différente que celle qu'il propose par défaut. Pour celà, il vous suffit de désactiver la boutique ci-après et d'installer la boutique concurrente dans le dossier ext/ à la racine.</em></p><hr>");
    $extform = $tb->AdminForm("boutique-extConf", true)
    ->addCheckField("en_boutique", "Activer la boutique par défaut :", $Serveur_Config['en_boutique'])
    ->addCheckField("boutique_ext", "Activer une boutique concurrente :", $Serveur_Config['boutique_ext']['en_boutique_externe'])
    ->addTextField("link_boutique_externe", "Lien vers votre boutique externe : (ignorer si boutique par défaut)", $Serveur_Config['boutique_ext']['link_boutique_externe']);
    $extform->addAPIButton($tb->AdminAPIButton("Sauvegarder", "btn btn-warning", LINK . "api/", "boutique", "set", "enBoutique", $extform , "", true ))
    ->setButtonsLine('class="text-right"');
    
    $panel_extshop = $tb->AdminPanel("Gérer une boutique externe", "fa-external-link ", $tb->UIArray(array($disablePreText, $extform)), "lg-6");
    
    
    // Le panel de l'ajout de catégories
    $addCatPreText = $tb->UiString("<em>Le nom de la nouvelle catégorie doit être unique. Il permet à vos clients de mieux identifier les différents types d'articles que vous proposez à la vente.</em><hr>");
    $new_cat = $tb->AdminForm("boutique-addCat", true)
    ->addTextField("cat_name", "Nom de la nouvelle catégorie", "", true, !$Serveur_Config['en_boutique']);
    $new_cat->addAPIButton($tb->AdminAPIButton("Ajouter", "btn btn-custom", LINK . "api/", "boutique", "set", "addCategory", $new_cat , "", true ))
    ->setButtonsLine('class="text-right"');
    
    $panel_addCat = $tb->AdminPanel("Ajouter une catégorie d'articles", "fa-cart-plus", $tb->UIArray(array($addCatPreText, $new_cat)), "lg-6");
    
    $mt = $controleur_def->getManualTasks();
    if (sizeof($mt) != 0)
        $adminBuilder->addColumn($tb->UIColumn("col-lg-12", array($tb->AdminCard("bg-danger", "<strong>Attention !</strong> Des tâches manuelles attendent d'être exécutées, des clients ne peuvent récupérer leurs achats ! <a class=\"text-white shadow\" href=\"" . LINK . "admin/boutique/tasks\">Rendez-vous ici</a> pour régulariser la situation.", ""), $tb->UIString("<br>"))));
    
    $adminBuilder->addColumn($tb->UIColumn("col-lg-6", array($panel_gen, $panel_extshop)));
    $adminBuilder->addColumn($tb->UIColumn("col-lg-6", array($panel_cat, $panel_addCat)));    
}


echo $adminBuilder->render();