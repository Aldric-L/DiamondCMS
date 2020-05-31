<?php 
$controleur_def->loadModel('admin/comptes');

//Si l'utilisateur n'a pas la permission de voir cette page
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 3){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}

//Si on passe en mode action (XHR) -------------------------------------------------
//On verifie que l'url correspond bien à une action
if (isset($param[3]) && !empty($param[3]) && isset($param[4]) && !empty($param[4])){
    $role_id = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "role", array(array("id", "=", $param[4])));
    if ($param[3] == "ban"){
        //On vérifie si l'utilisateur peut ban ce compte
        $compte = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "*", array(array("id", "=", intval($param[4]))));
        if (isset($_SESSION['user']) && !empty($_SESSION['user']) 
            && $compte['is_ban'] != true 
            && $_SESSION['user']->getId() != $param[4] 
            &&  $_SESSION['user']->getLevel() >= 4 
            && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(), $role_id['role']))){
            $can_ban = true;
        }else {
            $can_ban = false;
        }
    
        //On vérifie que l'utilisateur peut effectuer cette action
        if ($can_ban == true)
        {
          //On charge le model
          $controleur_def->loadModel('comptes/comptes');
          if ($param[3] == "ban"){
            if (isset($_POST['reason'])){
              banId($controleur_def->bddConnexion(), $param[4], $_POST['reason']);
              die("Success");
            }else {
              banId($controleur_def->bddConnexion(), $param[4]);
              die("Success");
            }
          }
        }else {
          die("Niveau d'autorisation non suffisant ou utilisateur déjà banni");
        }
    //Si on modifie les informations d'un utilisateur
    }else if ( $param[3] == "mod" && isset($_POST['money']) && isset($_POST['role']) ){
        $compte = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "*", array(array("id", "=", intval($param[4]))));
            //On vérifie que l'utilisateur en a la permission
        if (isset($_SESSION['user']) && !empty($_SESSION['user']) 
        && $_SESSION['user']->getId() != $compte['id'] 
        && $_SESSION['user']->getLevel() >= 4 
        && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(),$role_id['role']))){
            //On vérifie si on ne veut pas aussi débanir l'utilisateur
            if (isset($_POST['isban']) && !empty($_POST['isban'])){
                modify($controleur_def->bddConnexion(), $param[4], intval($_POST['money']), intval($_POST['role']), true);
                die('Success');
            }else {
                modify($controleur_def->bddConnexion(), $param[4], intval($_POST['money']), intval($_POST['role']));
                die('Success');
            }
        }else {
            die("Niveau d'autorisation insuffisant");
        }
    }else if ( $param[3] == "supp_profile_img" ){
            $compte = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "*", array(array("id", "=", intval($param[4]))));
            //On vérifie que l'utilisateur en a la permission
            if (isset($_SESSION['user']) && !empty($_SESSION['user']) 
            && $_SESSION['user']->getId() != $compte['id'] 
            && $_SESSION['user']->getLevel() >= 4 
            && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(),$role_id['role']))){
                //On vérifie si on ne veut pas aussi débanir l'utilisateur
                if (simplifySQL\update($controleur_def->bddConnexion(), "d_membre", array(array("profile_img", "=", "profiles/no_profile.png")), array(array("id", "=", intval($param[4]))))){
                    @unlink(ROOT . 'views/uploads/img/' . $compte['profile_img']);
                    die ('Success');
                }
                die('SQL Error');
            }else {
                die("Niveau d'autorisation insuffisant");
            }
    }
}
    


if (isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['level']) && !empty($_POST['level'])){
    addRole($controleur_def->bddConnexion(), $_POST['name'], $_POST['level']);
}
if (isset($param[2]) && !empty($param[2]) && $param[2] == "del_role" && isset($param[3]) && !empty($param[3])){
    if (delRole($controleur_def->bddConnexion(), intval($param[3]))){
        die("Success");
    }
    $controleur_def->addError(341);
}
if (isset($param[2]) && !empty($param[2]) && $param[2] == "permissions"){
    $permissions = simplifySQL\select($controleur_def->bddConnexion(), false, "d_roles", "*", false, "level");
    foreach ($permissions as $k => $p){
        $permissions[$k]['nb_users'] = 0;
    }
    //On sélectionne tous les comptes pour compter les membres de chaque role
    $comptes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_membre", "*", false, "pseudo");
    foreach ($comptes as $c){
        foreach ($permissions as $k => $p){
            if ($p['id'] == $c['role']){
                $permissions[$k]['nb_users'] = $permissions[$k]['nb_users'] + 1;
            }
        }
    }
    foreach ($permissions as $k => $p){
        if ( $permissions[$k]['nb_users'] != 0 || $permissions[$k]['name'] == "Membre" ||  $permissions[$k]['id'] == 1 ||  $permissions[$k]['id'] == 6 || $permissions[$k]['name'] == "diamond_master" ){
            $permissions[$k]['can_be_deleted'] = false;
        }else {
            $permissions[$k]['can_be_deleted'] = true;
        }
    }
    $controleur_def->loadViewAdmin('admin/comptes/permissions', 'accueil', 'Gestion des rôles');
}else if (isset($param[2]) && !empty($param[2]) && $param[2] == "list"){
    $comptes = simplifySQL\select($controleur_def->bddConnexion(), false, "d_membre", "*", false, "pseudo");
    $roles = simplifySQL\select($controleur_def->bddConnexion(), false, "d_roles", "*");

    //On définit les rôles que l'utilisateur peut assigner
    $rolescanbeselected = array();
    if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() >= 4){
        foreach ($roles as $r){
            if ($r['level'] <= $_SESSION['user']->getLevel()){
                array_push($rolescanbeselected, $r);
            }
        }
    }

    for ($i = 0; $i < sizeof($comptes); $i++) {
        foreach ($roles as $r){
            if ($r['id'] == $comptes[$i]['role']){
                $comptes[$i]['role_name'] = $r['name'];
                break;
            }
        }
        //On vérifie si l'utilisateur peut ban ce compte
        if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $comptes[$i]['is_ban'] != true && $_SESSION['user']->getId() != $comptes[$i]['id'] && $_SESSION['user']->getLevel() >= 4 && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(),$comptes[$i]['role']))){
            $comptes[$i]['can_ban'] = true;
        }else {
            $comptes[$i]['can_ban'] = false;
        }

        //On vérifie si l'utilisateur peut deban ce compte
        if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getId() != $comptes[$i]['id'] && $_SESSION['user']->getLevel() >= 4 && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(),$comptes[$i]['role']))){
            $comptes[$i]['can_deban'] = true;
        }else {
            $comptes[$i]['can_deban'] = false;
        }

        //On vérifie si l'utilisateur peut modifier ce compte
        if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getId() != $comptes[$i]['id'] && $_SESSION['user']->getLevel() >= 4 && intval($_SESSION['user']->getRole()) > intval($controleur_def->getRoleLevel($controleur_def->bddconnexion(),$comptes[$i]['role']))){
            $comptes[$i]['can_modify'] = true;
        }else {
            $comptes[$i]['can_modify'] = false;
        }
    }
    //var_dump($comptes, $_SESSION);
    $controleur_def->loadJS('admin/comptes/list');
    $controleur_def->loadViewAdmin('admin/comptes/list', 'accueil', 'Gestion des utilisateurs');
}