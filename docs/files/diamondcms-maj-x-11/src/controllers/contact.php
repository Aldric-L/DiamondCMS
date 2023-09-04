<?php 
if (isset($_POST) && !empty($_POST)
&& isset($_POST['name']) && !empty($_POST['name'])
&& isset($_POST['email']) && !empty($_POST['email'])
&& isset($_POST['message']) && !empty($_POST['message'])){
    $controleur_def->loadModel('contact');
    $controleur_def->loadModel("libs/htmlpurifier/standalone");
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $_POST['message'] = $purifier->purify($_POST['message']);

    addContact($controleur_def->bddConnexion(), $_POST['name'], htmlspecialchars($_POST['message']), htmlspecialchars($_POST['email']));
    $controleur_def->notify('Une nouvelle demande de contact disponible sur votre interface. ', "admin", 1, "Contact", "");
}

$controleur_def->loadView('pages/contact', '', 'Contact');