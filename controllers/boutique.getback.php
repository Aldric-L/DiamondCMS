<?php 
global $cm;
//Ce controlleur a pour fonction d'éxecuter les commandes pour permettre à l'utilisateur de recevoir son dû après un payement sur la boutique
//Il doit toujours être appelé avec un identifiant unique, qui permet de retrouver la commande (l'uuid est évidemment contrôlé)
if (!isset($param[1]) || empty($param[1]) || !isset($param[2]) || empty($param[2])){
    require_once (ROOT . "controllers/accueil.php");
    die;
}
$uuid = $param[2];
//On essaye de charger la commande 
$commande = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_achats", "*", array(array("uuid", "=", $uuid)));
//Pour les erreurs, on utilise pas l'utilitaire d'erreur de controleur_def puisqu'il sous-entend charger la vue en entier pour afficher un modal, or on ne peut pas prendre le risque de charger la page, donc on lève une erreur qui va interrompre l'éxecution de la page, et rediriger l'utilisateur.
if (empty($commande)){
    $erreur = "Impossible de charger la commande demandée : êtes-vous vraiment un acheteur ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.";
    $controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}
if (!isset($_SESSION['user']) || empty($_SESSION['user'])){
    $erreur = "Impossible de charger votre compte client : êtes-vous vraiment un acheteur ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.";
    $controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}
if ($_SESSION['user']->getId() != $commande['id_user']){
    $erreur = "Impossible de trouver la commande demandée : êtes-vous vraiment l'acheteur de cette dernière ? Sachez que toute tentative de piratage peut faire l'objet de poursuites.";
    $controleur_def->loadView('pages/boutique/getback', 'emptyServer', 'Récupération de l\'achat');
    die;
}

//On suppose que si l'on en arrive ici, c'est que l'utilisateur est authentique
//On charge les taches à faire
$tasks = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_todolist", "*", array(array("id_commande", "=", $commande['id'])));
foreach ($tasks as $k => $t){
    //On récupère la commande correspondante
    $tasks[$k]['cmd'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cmd", "*", array(array("id", "=", $t['cmd'])));    
    $tasks[$k]['cmd']['server_name'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['name'];
    $tasks[$k]['cmd']['server_game'] = $cm->getConfig()[$tasks[$k]['cmd']['server']]['game'];
}
var_dump($tasks, $commande);
$controleur_def->loadView('pages/boutique/getback', 'boutique', 'Récupération de l\'achat');
