<?php
//Ce controller permet de gérer les différentes pages du CMS, notamment celles qui peuvent être créées par l'administrateur
// Cette page n'est pas conforme aux normes API Rest 2022, et devra faire  l'objet d'une réécriture...

//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $controleur_def->loadViewAdmin('admin/onlyforadmins', 'accueil', 'Interdit');
    die;
}


// ----------------- AJAX HEADER


//Si on est en AJAX et qu'on cherche à créer un menu déroulant dans le header
if (isset($param[2]) && !empty($param[2]) && $param[2] == "header_newmd"){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\insert($controleur_def->bddConnexion(), "d_header_menus", array("name"), array("Mon nouveau menu")) == true){
        die ('Success');
    }
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à créer un lien dans le header
if (isset($param[2]) && !empty($param[2]) && $param[2] == "header_newmdlink" && isset($_POST['titre']) && isset($_POST['link'])){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\insert($controleur_def->bddConnexion(), "d_header_menus", array("name", "link", "is_menu"), array($_POST['titre'], $_POST['link'], 0)) == true){
        die ('Success');
    }
    die ('Error');
}


//Si on est en AJAX et qu'on cherche à supprimer un menu déroulant dans le header
if (isset($param[2]) && !empty($param[2]) && $param[2] == "header_delmd" && isset($param[3]) && !empty($param[3])){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\delete($controleur_def->bddConnexion(), "d_header_menus", array(array("id", "=", $param[3]))) == true &&
    simplifySQL\delete($controleur_def->bddConnexion(), "d_header_menus_pages", array(array("id_menu", "=", $param[3]))) == true){
            die ('Success');
    }
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à renommer un menu déroulant
if (isset($param[2]) && !empty($param[2]) && $param[2] == "header_renamemd" && isset($param[3]) && !empty($param[3]) && isset($_POST['val']) && !empty($_POST['val'])){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\update($controleur_def->bddConnexion(), "d_header_menus", array(array("name", "=", $_POST['val'])), array(array("id", "=", $param[3]))) == true){
        die ('Success');
    }
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à modifier l'ordre des pages du header
if (isset($param[2]) && !empty($param[2]) && $param[2] == "order" && isset($param[3]) && $param[3] == "header" && isset($param[4]) && isset($_POST['pos']) && isset($_POST['type']) && $_POST['type'] == "header"){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\update($controleur_def->bddConnexion(), "d_header_menus_pages", array(array("pos", "=", $_POST['pos'])), array(array("id", "=", $param[4]))) == true){
        die ('Success');
    }
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à désactiver une page du header
if (isset($param[2]) && !empty($param[2]) && $param[2] == "header_del" && isset($param[3])){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\delete($controleur_def->bddConnexion(), "d_header_menus_pages", array(array("id", "=", $param[3]))) == true){
        die ('Success');
    }
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à activer une page du header
if (isset($param[2]) && !empty($param[2]) && $param[2] == "header_add" && isset($param[3]) && isset($param[4]) && isset($param[5])){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\insert($controleur_def->bddConnexion(), "d_header_menus_pages", array("id_page", "pos", "id_menu"), array($param[5], $param[4], $param[3])) == true){
        die ('Success');
    }
    die ('Error');
}

// -------------------- FIN HEADER


// ----------------- AJAX FOOTER

//Si on est en AJAX et qu'on cherche à modifier l'ordre des pages du footer
if (isset($param[2]) && !empty($param[2]) && $param[2] == "order" && isset($param[3]) && !empty($param[3]) && $param[3] == "footer" && isset($param[4]) && !empty($param[4]) && isset($_POST['pos']) && isset($_POST['type']) && $_POST['type'] == "footer"){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\update($controleur_def->bddConnexion(), "d_footer", array(array("pos", "=", intval($_POST['pos']))), array(array("id", "=", intval($param[4])))) == true){
        die ('Success');
    }
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à modifier l'ordre des pages du footer en activant un lien
if (isset($param[2]) && !empty($param[2]) && $param[2] == "footer_add" && isset($param[3]) && !empty($param[3])){
    define('FORCE_INLINE_ERR', true);
    $others = simplifySQL\select($controleur_def->bddConnexion(), true, "d_footer", "*", array(array('disabled', "=", 0)), "pos", true);
    if (empty($others)){
        if (simplifySQL\update($controleur_def->bddConnexion(), "d_footer", array(array("disabled", "=", 0), array("pos", "=", 0)), array(array("id", "=", $param[3]))) == true){
            die ('Success');
        }
    }else {
        if (simplifySQL\update($controleur_def->bddConnexion(), "d_footer", array(array("disabled", "=", 0), array("pos", "=", intval($others['pos'], 10)+1)), array(array("id", "=", $param[3]))) == true){
            die ('Success');
        }
    }
    
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à modifier l'ordre des pages du footer en desactivant un lien
if (isset($param[2]) && !empty($param[2]) && $param[2] == "footer_del" && isset($param[3]) && !empty($param[3])){
    define('FORCE_INLINE_ERR', true);
    if (simplifySQL\update($controleur_def->bddConnexion(), "d_footer", array(array("disabled", "=", 1), array("pos", "=", NULL)), array(array("id", "=", $param[3]))) == true){
        die ('Success');
    }
    die ('Error');
}


// ----------------- FIN AJAX FOOTER


//Si on est en AJAX et qu'on cherche à supprimer une page
if (isset($param[2]) && !empty($param[2]) && $param[2] == "delete" && isset($param[3]) && !empty($param[3])){
    define('FORCE_INLINE_ERR', true);
    if ($controleur_def->delPage(false, $param[3])){
        die('Success');
    }
    die ('Error');
}

//Si on est en AJAX et qu'on cherche à supprimer un lien
if (isset($param[2]) && !empty($param[2]) && $param[2] == "delete_link" && isset($param[3]) && !empty($param[3])){
    define('FORCE_INLINE_ERR', true);
    $name_raw = simplifySQL\select($controleur_def->bddConnexion(), true, "d_header", "*", array(array("id", "=", $param[3])));
    if (!$name_raw || !isset($name_raw['link'])){
        die('Error');
    }
    if ($controleur_def->delPage(true, $name_raw['link'])){
        die('Success');
    }
    die ('Error');
}

//Si on charge la page de modification
/*
Depuis la 2.0, on utilise directement l'édition inline de tinyMCE
if (isset($param[2]) && !empty($param[2]) && $param[2] == "modify" && isset($param[3]) && !empty($param[3])){
    //On commence par chercher la page à modifier
    $result = simplifySQL\select($controleur_def->bddConnexion(), true, "d_pages", "*", array(array("name_raw", "=", $param[3])));
    if (is_array($result)){
        $file = $result['file_name'];
        $page_name = $result['name'];
        $page_raw = $param[3];
        if (!file_exists(ROOT . 'config/' . $file . '.ftxt')){
            $controleur_def->addError(132);
            header('Location: ' . LINK . 'admin/pages/');
            die;
        }
        // Si l'on passe en mode modification des fichiers config (requettes POST via AJAX)
        if (isset($_POST['content'])){
            $fp = fopen (ROOT . "config/" . $file . ".ftxt", "w");
            if (!$fp){
                $controleur_def->addError(111);
            }
            fseek ($fp, 0);
            fputs ($fp, $_POST['content']);
            fclose ($fp);
        }

        $current = file_get_contents(ROOT . "config/" . $file . ".ftxt");
        $controleur_def->loadJS('admin/pages/modify');
        $controleur_def->loadViewAdmin('admin/modify.pages', 'accueil', 'Gestion des pages - Page ' . $page_name);
    }
    die;
}*/

if (!empty($_POST) && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['name_raw']) && !empty($_POST['name_raw'])){
    if (isset($_POST['fa_icon'])){
        if (!$controleur_def->addPage(false, $_POST['name_raw'], $_POST['name'], $_POST['fa_icon'])){
            $controleur_def->addError(350);
        }
    }else {
        if (!$controleur_def->addPage(false, $_POST['name_raw'], $_POST['name'])){
            $controleur_def->addError(350);
        }
    }
}
if (!empty($_POST) && isset($_POST['name_newlink']) && !empty($_POST['name_newlink']) && isset($_POST['link_newlink']) && !empty($_POST['link_newlink'])){
        if (!$controleur_def->addPage(true, $_POST['link_newlink'], $_POST['name_newlink'])){
            $controleur_def->addError(350);
        }
}

//On récupère toutes les pages
$pages = $controleur_def->getPages();

//On récupère les pages du footer
$footer_pages = simplifySQL\select($controleur_def->bddConnexion(), false, "d_footer", "*", false, "pos");
$total_f_pages = 0;
foreach ($footer_pages as $key => $fp){
    if (isset($fp['id_page']) && !empty($fp['id_page']) && $fp['id_page'] != null){
        $footer_pages[$key]['titre'] = @simplifySQL\select($controleur_def->bddConnexion(), true, "d_pages", "*", array(array("id", "=", $fp['id_page'])))['name'];
    }
    if ($fp['disabled'] == 0){
        ++$total_f_pages;
    }
}

//On récupère les menus déroulant du header
$header_md = simplifySQL\select($controleur_def->bddConnexion(), false, "d_header_menus", "*", false);
//On commence par traiter les pages disponibles pour les menus déroulant en général
$available_pages = simplifySQL\select($controleur_def->bddConnexion(), false, "d_header", "*");
foreach ($available_pages as $key => $ap) {
    //Si l'id de la page n'est pas null, c'est que c'est bien une réference
    if ($ap['id_page'] != NULL){
        //On va chercher dans la table d_pages + d'infos comme le titre de la page (en effet, dans d_header_menus_pages, on a que des réferences vers les autres pages)
        $available_pages[$key]['titre'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_pages", "name, name_raw", array(array("id", "=", $ap['id_page'])));
        //Si on trouve bien on complète les champs
        if (isset($available_pages[$key]['titre']['name'])){
            $nr = $available_pages[$key]['titre']['name_raw'];
            $available_pages[$key]['titre'] = $available_pages[$key]['titre']['name'];
            $available_pages[$key]['link'] = $nr;
        }else {
            $controleur_def->addError("343b");
        }
    }
}
//Maintenant on travaille menu par menu
foreach ($header_md as $k =>$md){
    //On récupère la référence des pages enregistrées
    $header_md[$k]['pages'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_header_menus_pages", "*", array(array("id_menu", "=", $md['id'])), "pos");
    foreach ($header_md[$k]['pages'] as $key => $hp) {
        // On convertit les références
        $header_md[$k]['pages'][$key]['header_page'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_header", "*", array(array("id", "=", $hp['id_page'])));
        //Si la requête aboutit
        if ($header_md[$k]['pages'][$key]['header_page'] != false){
            $header_md[$k]['pages'][$key]['id_page'] = $header_md[$k]['pages'][$key]['header_page']['id_page'];
            $header_md[$k]['pages'][$key]['titre'] = $header_md[$k]['pages'][$key]['header_page']['titre'];
            $header_md[$k]['pages'][$key]['link'] = $header_md[$k]['pages'][$key]['header_page']['link'];
            //Si l'id de la page n'est pas null, c'est que c'est encore une réference vers une page de d_page
            if ($header_md[$k]['pages'][$key]['id_page'] != NULL){
                //On va chercher dans la table d_pages + d'infos comme le titre de la page (en effet, dans d_header_menus_pages, on a que des réferences vers les autres pages)
                $header_md[$k]['pages'][$key]['titre'] = simplifySQL\select($controleur_def->bddConnexion(), true, "d_pages", "name, name_raw", array(array("id", "=", $header_md[$k]['pages'][$key]['id_page'])));
                //Si on trouve bien on complète les champs
                if (isset($header_md[$k]['pages'][$key]['titre']['name'])){
                    $nr = $header_md[$k]['pages'][$key]['titre']['name_raw'];
                    $header_md[$k]['pages'][$key]['titre'] = $header_md[$k]['pages'][$key]['titre']['name'];
                    $header_md[$k]['pages'][$key]['link'] = $nr;
                //Sinon on lève une erreur puisqu'il n'est pas normal de ne pas trouver une page dont on a la réference => la BDD est alors corrompue
                }else {
                    $controleur_def->addError("343b");
                }
            }
        }else {
            $controleur_def->addError("343b");
            break;
        }
        
    }
    //Maintenant, à partir de la liste des pages disponibles que l'on a récupéré, on cherche quelles pages doivent être proposées
    $header_md[$k]['available_pages'] = $available_pages;
    //Pour cela, on vérifie chaque page afin de savoir si celle-ci n'est pas déjà ajoutée dans le menu
    //On créé un tableau d'index à supprimer (evidemment, on ne peut pas directement supprimer les pages indisponibles dans la boucle à cause de l'incrémentation)
    $mustbedeleted = array();
    foreach ($header_md[$k]['available_pages'] as $n => $ap){
        foreach ($header_md[$k]['pages'] as $p){
            //Si c'est le cas
            //var_dump($header_md[$k]['available_pages'][$n], $p);
            if (isset($header_md[$k]['available_pages'][$n]) && isset($p['link']) && $header_md[$k]['available_pages'][$n]['link'] == $p['link'] ){
                //On ajoute la page à supprimer dans notre "death list"
                array_push($mustbedeleted, $n);
                //On casse la seconde boucle qui correspond à la page disponible que l'on a finalement rejetée
                break;
                //On retourne donc dans la boucle qui vérifie les autres pages
            }
        }
    }
    //On execute la death list des pages indisponibles car déjà enregistrées dans le menu déroulant
    foreach ($mustbedeleted as $d){
        //On la supprime
        unset($header_md[$k]['available_pages'][$d]);
    }
    //Et on finit par réordonner le tableau pour ne pas avoir de problèmes d'indexation
    sort($header_md[$k]['available_pages']);
}

//On récupère la liste des liens
$links = simplifySQL\select($controleur_def->bddConnexion(), false, "d_header", "*", "id_page IS NULL");

//Si aucun test n'a aboutit, c'est qu'on est bien sur la page principale de la fonction page
$controleur_def->loadJS('admin/pages/pages');
$controleur_def->loadViewAdmin('admin/pages', 'accueil', 'Gestion des pages');