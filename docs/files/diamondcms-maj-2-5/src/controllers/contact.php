<?php 
if (isset($_POST) && !empty($_POST)
&& isset($_POST['name']) && !empty($_POST['name'])
&& isset($_POST['email']) && !empty($_POST['email'])
&& isset($_POST['message']) && !empty($_POST['message'])){
    $controleur_def->loadModel('contact');
    addContact($controleur_def->bddConnexion(), $_POST['name'], $_POST['message'], $_POST['email']);
    $controleur_def->notify('Une nouvelle demande de contact disponible sur votre interface. ', "admin", 1, "Contact", "");
}

$controleur_def->loadView('pages/contact', '', 'Contact');