<?php 

$boutique_config = parse_ini_file(ROOT . 'config/boutique.ini', true);

if (isset($param[2]) && !empty($param[2]) && $param[2] == "getback-DDPass"){

    $code = isset($_POST['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_POST['code']) : ''; 
    $errors = false;
    if( empty($code) ) { 
        $errors = true;
        $money_get = 0;
        $payement_type = "DDP";
        $controleur_def->loadView('pages/boutique/money_getback_success', 'boutique', 'Achat de ' . $Serveur_Config['Serveur_money'] . 's');
        die;
    } 
    else { 
        $dedipass = file_get_contents('http://api.dedipass.com/v1/pay/?public_key=' . $boutique_config['DediPass']['public_key'] . '&private_key=' . $boutique_config['DediPass']['private_key'] . '&code=' . $code);
        $dedipass = json_decode($dedipass); 
        if($dedipass->status == 'success') { 
            // Le transaction est validée et payée. 
            // Vous pouvez utiliser la variable $virtual_currency 
            // pour créditer le nombre de Jetons. 
            $virtual_currency = $dedipass->virtual_currency; 
            $rate = $dedipass->rate;
            $payout = $dedipass->payout;
            $code = $dedipass->code;
            if($virtual_currency == 0 OR $virtual_currency == NULL){
                $virtual_currency = 1;
            }
            if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_dedipass", 
            array("id_user", "code", "payout", 'virtual_currency', 'date'),
            array($_SESSION['user']->getId(), $code, $payout, $virtual_currency, date("Y-m-d H:i:s"))
            )){
                $errors = true;
            }else {
                $_SESSION['user']->credit($controleur_def->bddConnexion(), intval($virtual_currency));
                $_SESSION['user']->reload($controleur_def->bddConnexion());
                $money_get = $virtual_currency;
                $payement_type = "DDP";
                $controleur_def->loadView('pages/boutique/money_getback_success', 'boutique', 'Achat de ' . $Serveur_Config['Serveur_money'] . 's');
                die;
            }
        }else { 
            // Le code est invalide 
            $errors = true;
            $money_get = 0;
            $payement_type = "DDP";
            $controleur_def->loadView('pages/boutique/money_getback_success', 'boutique', 'Achat de ' . $Serveur_Config['Serveur_money'] . 's');
            die;
        } 
    } 

//Ici on crée le payement via PAYPAL
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "createpaypal" && isset($param[3]) && !empty($param[3])){
    //On vérifie que PayPal est bien activé
    if (!$boutique_config['PayPal']['en_paypal']){
        die('Payer par PayPal n\'a pas été autorisé');
    }

    //Ensuite on cherche l'offre que l'utilisateur a selectionné
    $offre = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_paypal_offres", "*", array(array("id", "=", $param[3])));
    if (empty($offre)){
        die('Impossible de trouver l\'offre demandée');
    }

    $controleur_def->loadModel('paypal.class');

    $success = 0;
    $msg = "Une erreur est survenue, merci de bien vouloir réessayer ultérieurement...";
    $paypal_response = [];

    if (!isset($_SESSION['user']) || empty($_SESSION['user'])){
        $success = 0;
        $msg = "Vous devez être connecté pour recevoir votre dû.";
        echo json_encode(["success" => $success, "msg" => $msg, "paypal_response" => $paypal_response]);
        die;
    }

    $payer = new PayPalPayment();
    if ($boutique_config['PayPal']['sandbox']){
        $payer->setSandboxMode(1); 
    }
    $payer->setClientID($boutique_config['PayPal']['id']); 
    $payer->setSecret($boutique_config['PayPal']['secret']);

    $payment_data = [
    "intent" => "sale",
    "redirect_urls" => [
        "return_url" => $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT,
        "cancel_url" => $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT
    ],
    "payer" => [
        "payment_method" => "paypal"
    ],
    "transactions" => [
        [
            "amount" => [
                "total" => $offre['price'],
                "currency" => $boutique_config['PayPal']['money']
            ],
            "item_list" => [
                "items" => [
                [
                    "sku" => $offre['uuid'],
                    "quantity" => "1",
                    "name" => $offre['name'],
                    "price" => $offre['price'],
                    "currency" => $boutique_config['PayPal']['money']
                ]
                ]
            ],
            "description" => "Achat de" . $Serveur_Config['Serveur_money'] . "s"
        ]
    ]
    ];
    
    $paypal_response = $payer->createPayment($payment_data);
    $paypal_response = json_decode($paypal_response);

    if (!empty($paypal_response->id)) {
        if ( !simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_paypal", 
        array("payment_id", "payment_status", "payment_amount", "payment_currency", "payment_date", "user", "money_get"), 
        array( $paypal_response->id, $paypal_response->state, $paypal_response->transactions[0]->amount->total, $paypal_response->transactions[0]->amount->currency, date("Y-m-d H:i:s"), $_SESSION['user']->getId(), $offre['tokens'] ) 
        ) ){
            $success = 0;
            $msg = "SQL Error";
            echo json_encode(["success" => $success, "msg" => $msg, "paypal_response" => $paypal_response]);
        }
        $success = 1;
        $msg = "";
    }else {
        $msg = "Une erreur est survenue durant la communication avec les serveurs de PayPal. Merci de bien vouloir réessayer ultérieurement.";
     }
     echo json_encode(["success" => $success, "msg" => $msg, "paypal_response" => $paypal_response]);
     die;

//Ici on execute le payement via PAYPAL
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "buypaypal" && isset($param[3]) && !empty($param[3])){
    //On vérifie que PayPal est bien activé
    if (!$boutique_config['PayPal']['en_paypal']){
        die('Payer par PayPal n\'a pas été autorisé');
    }

    $controleur_def->loadModel('paypal.class');

    if (!empty($_POST['paymentID']) AND !empty($_POST['payerID'])) {
        $paymentID = htmlspecialchars($_POST['paymentID']);
        $payerID = htmlspecialchars($_POST['payerID']);
      
        $payer = new PayPalPayment();
        if ($boutique_config['PayPal']['sandbox']){
            $payer->setSandboxMode(1); 
        }
        $payer->setSandboxMode(1);$payer->setClientID($boutique_config['PayPal']['id']); 
        $payer->setSecret($boutique_config['PayPal']['secret']);

        $payment = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_paypal", "*", array(array("payment_id", "=", $paymentID)));
      
        if ($payment == true || !empty($payment)) {
           $paypal_response = $payer->executePayment($paymentID, $payerID);
           $paypal_response = json_decode($paypal_response);
      
           if (!simplifySQL\update($controleur_def->bddConnexion(), "d_boutique_paypal", array(
               array("payment_status", "=", $paypal_response->state),
                array("payer_email", "=", $paypal_response->payer->payer_info->email)), 
            array(array("payment_id", "=", $paymentID)))){
                $success = 0;
                $msg = "Erreur SQL update";
                echo json_encode(["success" => $success, "msg" => $msg, "paypal_response" => $paypal_response]);
                die;
           }
      
           if ($paypal_response->state == "approved") {
                //On crédite le compte client 
                $compte = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "*", array(array("id", "=", $payment['user'])));
                if (empty($compte) || $compte == false){
                    $success = 0;
                    $msg = "Impossible de trouver votre compte client";
                    echo json_encode(["success" => $success, "msg" => $msg, "paypal_response" => $paypal_response]);
                    die;
                }
                $money_get = $payment['money_get'];
                if (!simplifySQL\update($controleur_def->bddConnexion(), "d_membre", array(array("money", "=", intval($compte['money']) + intval($money_get))), array(array("id", "=", $payment['user'])))){
                    $success = 0;
                    $msg = "Impossible de créditer votre compte";
                }
              $success = 1;
              $msg = "";
              $_SESSION['user']->reload($controleur_def->bddConnexion());
           } else {
              $msg = "Une erreur est survenue durant l'approbation de votre paiement. Merci de réessayer ultérieurement ou contacter un administrateur du site.";
           }
        } else {
           $msg = "Votre paiement n'a pas été trouvé dans notre base de données. Merci de réessayer ultérieurement ou contacter un administrateur du site. (Votre compte PayPal n'a pas été débité)";
        }
    }
     echo json_encode(["success" => $success, "msg" => $msg, "paypal_response" => $paypal_response]);
     die;

}else if(isset($param[2]) && !empty($param[2]) && $param[2] == "successpaypal"){
    $payement_type = "PP";
    $controleur_def->loadView('pages/boutique/money_getback_success', 'boutique', 'Achat de ' . $Serveur_Config['Serveur_money'] . 's');
    die;
}

$paypal_offres = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_paypal_offres", "*");

$controleur_def->loadView('pages/boutique/money', 'boutique', 'Achat de ' . $Serveur_Config['Serveur_money'] . 's');
