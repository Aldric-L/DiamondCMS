<?php 
$controleur_def->loadModel('admin/accueil');

$errors_content = analiserLog($controleur_def, 22);

$controleur_def->loadViewAdmin('admin/errors', 'accueil', 'Erreurs du CMS');