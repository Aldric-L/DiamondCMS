<?php
//Si le module permettant de se lier aux serveurs est activé
if (defined("DServerLink") && DServerLink == true){
    //On initie les connexions avec les serveurs liés
    $linkmc->connect();

    if (!empty($param[1])){
        //Si on passe en mode transmission de données (AJAX)
        //et si on cherche à obtenir les informations sur un seul serveur
        if ($param[1] == "json" && isset($param[2]) && !empty($param[2])){
            $servers = $linkmc->getInfoOnServer(intval($param[2]));
            echo json_encode($servers, JSON_NUMERIC_CHECK);
        //ou si on cherche à obtenir des informations sur tous les serveurs
        }else if ($param[1] == "json"){
            $servers = $linkmc->getInfoOnServers();
            $fichier_save = "";
            echo json_encode($servers, JSON_NUMERIC_CHECK);

        //Sinon, on passe en mode affichage
        }else {
            //On verifie qu'un serveur est bien demandé
            if (intval($param[1])){
                $server_id = $param[1];
                $servers = $linkmc->getInfoOnServer($server_id);
                $connect = 0;
                //Si on ne reçoit rien, on considère que le serveur n'est pas connecté
                if ($servers['results'] == false){

                    $empty = true;
                    $controleur_def->loadView('pages/serveurs', 'emptyServer', 'Serveurs');
                    die();
                }
                $players = $linkmc->getPlayers(intVal($param[1]));
                $controleur_def->loadView('pages/serveurs', '', 'Serveurs');
            }else {
                header('Location: '. $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);
            }   
        }
        
    }else {
        header('Location: '. $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);
    }
}else {
    header('Location: '. $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);
}