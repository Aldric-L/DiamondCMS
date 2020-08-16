<?php
//Si le module permettant de se lier aux serveurs est activé
if (defined("DServerLink") && DServerLink == true){
    
    //On initie les connexions avec les serveurs liés
    $servers_link->connect();

    if (!empty($param[1])){
        //Si on passe en mode transmission de données (AJAX)
        //et si on cherche à obtenir les informations sur un seul serveur
        if ($param[1] == "json" && isset($param[2]) && !empty($param[2])){
            $servers = $servers_link->getInfos(intval($param[2]));
            $servers_link->disconnect();
            
            echo json_encode($servers, JSON_NUMERIC_CHECK);
        //ou si on cherche à obtenir des informations sur tous les serveurs
        }else if ($param[1] == "json"){
            $servers = $servers_link->getInfos();
            $servers_link->disconnect();
            $fichier_save = "";
            echo json_encode($servers, JSON_NUMERIC_CHECK);

        //Sinon, on passe en mode affichage
        }else {
            //On verifie qu'un serveur est bien demandé
            if (intval($param[1])){
                $server_id = intVal($param[1]);
                $servers = $servers_link->getInfos($server_id);
                $connect = 0;
                //Si on ne reçoit rien, on considère que le serveur n'est pas connecté
                if (empty($servers) || $servers[intVal($param[1])]['results'] == false){

                    $empty = true;
                    $controleur_def->loadView('pages/serveurs', 'emptyServer', 'Serveurs');
                    die();
                }
                $players = $servers_link->getPlayers(intVal($param[1]));
                $game = $servers_link->getGame($server_id);
                $servers_link->disconnect();
                $controleur_def->loadView('pages/serveurs', '', 'Serveurs');
            }else {
                header('Location: '. LINK);
            }   
        }
        
    }else {
        header('Location: '. LINK);
    }
}else {
    header('Location: '. LINK);
}