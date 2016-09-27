<?php
//On récupère le(s) potentielle(s) erreurs due(s) au vote.
global $erreur_vote;
//On instancie le controleur en lui passant l'acces aux fichiers config
$controleur_def = new Controleur($Serveur_Config);

//On charge la vue, la fonction va charger 3 fichiers.
$controleur_def->loadView('pages/accueil', 'accueil', 'Accueil');
