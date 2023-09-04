<?php

if (!(defined("DServerLink") && DServerLink == true)
    || !(!empty($param[2]) && is_numeric($param[2])))
    header('Location: '. LINK);
    
$server_id = intval($param[2]);
$servers_link->connect($server_id);
$game = $servers_link->getGame($server_id);
$servers = $servers_link->getInfos($server_id);

//Si on ne reçoit rien, on considère que le serveur n'est pas connecté
if (empty($servers) || $servers[$server_id]['results'] == false)
    die($controleur_def->nonifyPage("Impossible de poursuivre", "Le serveur demandé n'est pas connecté.", "Rechargez la page dans quelques minutes..."));

$players = $servers_link->getPlayers($server_id);
if (!empty($game) && $game == "Minecraft JSONAPI"){
    for ($i= 0; $i < sizeof($players[$server_id]['results']); $i++){
        $players[$server_id]['results'][$i]['N'] = $players[$server_id]['results'][$i]['name'];
    }
}
$servers_link->disconnect($server_id);
$controleur_def->loadViewAddon(ROOT . 'addons/Diamond-ServerLink/views/serveurs.php', '', 'Serveurs');
