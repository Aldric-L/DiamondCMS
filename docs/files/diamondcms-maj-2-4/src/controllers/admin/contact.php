<?php 
//Si on passe en mode XHR
if (isset($param[2]) && !empty($param[2]) && $param[2] == "delete" && isset($param[3]) && !empty($param[3])){
    if (simplifySQL\delete($controleur_def->bddConnexion(), "d_contact", array(array("id", "=", $param[3]))) != true){
        die('Error');
    }else {
        die('Success');
    }
}


$contacts = simplifySQL\select($controleur_def->bddConnexion(), false, "d_contact", "*");

$controleur_def->loadJS("admin/contact");
$controleur_def->loadViewAdmin('admin/contact', 'accueil', 'Interface de contact');