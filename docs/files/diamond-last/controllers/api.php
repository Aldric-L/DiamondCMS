<?php 

/**
 * API : Controleur en charge de traduire les requêtes AJAX des utilisateurs pour exécuter l'API (admin ou non)
 * Les modules se trouvent dans models/API ou dans les dossiers des addons (models/API)
 * Cette page est conçue pour fonctionner avec ajax-simpleSend
 * 
 * $param[1] : module
 * $param[2] : verbe (SET/GET)
 * $param[3] : fonction
 */

if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2])){

    $controleur_def->loadModel("api.class");

    if (!(isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2]) && isset($param[3]) && is_string($param[3])))
        die (json_encode(array("State"=> "0", "Errors" => array("native$701", "Missing arguments"))));

    try {
        $rtrn = DiamondAPI::execute(true, $controleur_def, $controleur_def->getAvailableAddons(), 
                $param[1], $param[2], $param[3], 
                (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user']->getLevel() : -1,
                (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user'] : null, 
                (!empty($_POST)) ? $_POST : null);

        if (!is_array($rtrn) && array_key_exists("json_result", $rtrn))
            die (json_encode(array("State"=> "0", "Errors" => array("native$704","No answer available"))));
            
        die ($rtrn['json_result']);
    }catch (DiamondException $e){
        if ($e->getMessage() != $e->getVanillaMessage())
            die ( json_encode( array( "State"=> "0", "Errors" => array( $e->getTrueCode() , $e->getVanillaMessage() ) ) ) );

        die (json_encode(array("State"=> "0", "Errors" => array($e->getTrueCode(),$e->getMessage()))));
    }catch (Throwable $e){
        die (json_encode(array("State"=> "0", "Errors" => array("native$704","Fatal Error : " . $e->getMessage() . " (True Code: " . $e->getCode() . " l." . $e->getLine() . " in " . $e->getFile() . ")"))));
    }

}

die (json_encode(array("State"=> "0", "Errors" => array("native$701", "Missing arguments"))));
