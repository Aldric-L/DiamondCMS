<?php 
$controleur_def->loadModel('admin/comptes');


if (isset($param[2]) && !empty($param[2]) && $param[2] == "permissions"){
    if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
        $tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
        $adminBuilder = $tb->AdminBuilder("Vous n'avez pas l'autorisation d'accéder à ces réglages", "Veuillez contacter un administrateur pour obtenir un grade plus élevé.");
        echo $adminBuilder->render();
        die;
    }

    $controleur_def->loadModel("hydratation/roleHydrate.class");
    $permissions = simplifySQL\select($controleur_def->bddConnexion(), false, "d_roles", "*", false, "level");
    foreach ($permissions as $k => $p){
        $permissions[$k] = new RoleHydrate($controleur_def->bddConnexion(), false, $p);
    }
    $controleur_def->loadViewAdmin('admin/comptes/permissions', 'accueil', 'Gestion des rôles');
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "list"){
    if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 3){ 
        $tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
        $adminBuilder = $tb->AdminBuilder("Vous n'avez pas l'autorisation d'accéder à ces réglages", "Veuillez contacter un administrateur pour obtenir un grade plus élevé.");
        echo $adminBuilder->render();
        die;
    }

    $comptes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_membre", "*", false, "pseudo");
    $controleur_def->loadModel("hydratation/userHydrate.class");
    foreach ($comptes as $key => $compte) {
        $comptes[$key] = new UserHydrate($compte['pseudo'], $controleur_def->bddConnexion(), $_SESSION['user']);
    }
    $controleur_def->loadJS('admin/comptes/list');
    $controleur_def->loadViewAdmin('admin/comptes/list', 'accueil', 'Gestion des utilisateurs');

}else {
    header('Location: ' . LINK . "admin/404");
}