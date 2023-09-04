<?php 
$comptes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_membre", "*", false, "pseudo");
$controleur_def->loadModel("hydratation/userHydrate.class");
foreach ($comptes as $key => $compte) {
    $comptes[$key] = new UserHydrate($compte['pseudo'], $controleur_def->bddConnexion(), (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) ? $_SESSION['user'] : null);
    if ($comptes[$key]->isBanned())
        unset($comptes[$key]);
}
$controleur_def->loadView('pages/membres', '', 'Membres');