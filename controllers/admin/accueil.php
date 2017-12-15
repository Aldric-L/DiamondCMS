<?php 
$controleur_def->loadModel('admin/accueil');

$nb_coms = getNActionsForum($controleur_def->bddConnexion());

$errors = getNumberErrorLog();

$errors_content = analiserLog($controleur_def, 10);

$nb_tickets = getNumberTickets($controleur_def->bddConnexion());

$infos_cms = parse_ini_string(@file_get_contents("http://api.diamondcms.fr/status_cms.php?id=356a192b7913b04c54574d18c28d46e6395428ab"));

$controleur_def->loadViewAdmin('admin/accueil', 'accueil', 'Accueil');
