<?php
//$servers = $jsonapi->getInfoOnServers();
$n_serveurs = $jsonapi->getNumberServers();

$staff = select($controleur_def->bddConnexion(), false, "d_membre", "*", array(array("staff", "=", 1)));

$news = select($controleur_def->bddConnexion(), false, "d_news", "*", false, "date", true, array(0, 1));

$controleur_def->loadView('pages/accueil', 'accueil', 'Accueil');
