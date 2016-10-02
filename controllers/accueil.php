<?php
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
$controleur_def->loadView('pages/accueil', 'accueil', 'Accueil');
