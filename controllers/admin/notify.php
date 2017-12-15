<?php 

$notify_page = select($controleur_def->bddConnexion(), false, "d_notify", "*", "", "", false, 25);

$controleur_def->loadViewAdmin('admin/notify', 'accueil', 'Erreurs du CMS');