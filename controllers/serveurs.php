<?php
//$controleur_def->loadModel('serveurs');

if (!empty($param[1])){
    if ($param[1] == "json" && isset($param[2]) && !empty($param[2])){
        $servers = $jsonapi->getInfoOnServers(intval($param[2]));
        $fichier_save = "";
        //header("Content-type: text/ini");
        /*foreach($servers as $key => $item_n){
            $fichier_save .= "\n".$key.' = "'.$item_n . '"';
        }
        $fichier_save = substr($fichier_save, 1);
        echo $fichier_save;*/
        echo /*"data = '" .*/json_encode($servers, JSON_NUMERIC_CHECK)/* . "';" */;
    }else if ($param[1] == "json"){
        $servers = $jsonapi->getInfoOnServers();
        $fichier_save = "";
        //header("Content-type: text/ini");
        /*foreach($servers as $key => $item_n){
            $fichier_save .= "\n".$key.' = "'.$item_n . '"';
        }
        $fichier_save = substr($fichier_save, 1);
        echo $fichier_save;*/
        echo /*"data = '" .*/json_encode($servers, JSON_NUMERIC_CHECK)/* . "';" */;
    }else {
        if (intval($param[1])){
            $server_id = $param[1];
            $servers = $jsonapi->getInfoOnServers($server_id);
            $i = $jsonapi->getPlayersOnline();
            //var_dump($i[0][0]['success'][0]);
            $connect = 0;
            if ($servers['Connect'] == false){
                $empty = true;
                $controleur_def->loadView('pages/serveurs', 'emptyServer', 'Serveurs');
                die();
            }
            echo is_int($param[1]);
            $players = $jsonapi->getServeursConnect();
            var_dump($players);
            $controleur_def->loadView('pages/serveurs', '', 'Serveurs');
        }   
    }
    
}else {
    header('Location: '. $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT);
}