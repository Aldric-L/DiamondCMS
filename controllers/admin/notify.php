<?php 

$notify_page =  simplifySQL\select($controleur_def->bddConnexion(), false, "d_notify", "*", array(array("USER", "=", "admin")), "", false, 25);

$controleur_def->loadViewAdmin('admin/notify', 'accueil', 'Erreurs du CMS');