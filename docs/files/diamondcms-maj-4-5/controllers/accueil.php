<?php
if (defined("DServerLink") && DServerLink){
    $n_serveurs = $servers_link->getNbServers();
}

$membres = simplifySQl\select($controleur_def->bddConnexion(), false, "d_membre", "*", false);
$staff = array();
for($i =0; $i < sizeof($membres); $i++){
    if (intval($controleur_def->getRoleLevel($controleur_def->bddConnexion(), $membres[$i]['role'])['level']) >= 3){
        $membres[$i]['role_name'] = $controleur_def->getRoleNameById($controleur_def->bddConnexion(), $membres[$i]['role']);
        if (!$membres[$i]['is_ban'])
            array_push($staff, $membres[$i]);
    }
}
$news = simplifySQl\select($controleur_def->bddConnexion(), false, "d_news", "*", false, "date", true, array(0, 3));
foreach ($news as &$n){
    $n['user'] = $controleur_def->getPseudo($n['user']);
}


$controleur_def->loadJS('accueil');
$controleur_def->loadView('pages/accueil', 'accueil', 'Accueil');
