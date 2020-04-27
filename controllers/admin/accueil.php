<?php 
$controleur_def->loadModel('admin/accueil');

$nb_coms = getNActionsForum($controleur_def->bddConnexion());

$errors = getNumberErrorLog();

$errors_content = analiserLog($controleur_def, 10);

$nb_tickets = getNumberTickets($controleur_def->bddConnexion());

if (defined("DServerLink")){
    $n_serveurs = $servers_link->getNbServers();
}

//$infos_cms = parse_ini_string(@file_get_contents($Serveur_Config['api_url'] . "status_cms.php?id=356a192b7913b04c54574d18c28d46e6395428ab"));
/*var_dump($infos_cms);
exit;*/
$controleur_def->loadJS('admin/accueil');
$controleur_def->loadViewAdmin('admin/accueil', 'accueil', 'Accueil');
