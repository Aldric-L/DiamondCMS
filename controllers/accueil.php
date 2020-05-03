<?php
<<<<<<< HEAD
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
$controleur_def->loadJS('accueil');
=======
//On récupère le(s) potentielle(s) erreurs due(s) au vote.
global $erreur_vote;
//On instancie le controleur en lui passant l'acces aux fichiers config
$controleur_def = new Controleur($Serveur_Config);

$controleur_def->loadModel('vote');
//On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
$voteurs = bestVotes($controleur_def->bddConnexion());
//On parcourt le tableau retourner par bestVotes
foreach ($voteurs as $key => $voteur) {
  //On les mets en forme
  $voteurs[$key]['pseudo'] = $voteur['pseudo'];
  $voteurs[$key]['votes'] = $voteur['votes'];
}


//On charge la vue, la fonction va charger 3 fichiers.
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
$controleur_def->loadView('pages/accueil', 'accueil', 'Accueil');
