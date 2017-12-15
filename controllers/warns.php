<?php 
if ($Serveur_Config['en_d_warns'] && isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo'])){
    $enabled = true;
}else {
    $enabled = false;
}

$controleur_def->loadView('pages/warns', '', 'Warns');