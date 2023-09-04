<?php 
//Si l'utilisateur n'a pas la permission de voir cette page
//Cette page est réservée au grade diamond_master
if (isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getLevel() <= 4){ 
    $adminBuilder = $tb->AdminBuilder("Vous n'avez pas l'autorisation d'accéder à ces réglages", "Veuillez contacter un administrateur pour obtenir un grade plus élevé.");
    echo $adminBuilder->render();
    die;
}

$tb = new PageBuilders\ThemeBuilder($Serveur_Config['theme']);
$adminBuilder = $tb->AdminBuilder("Configuration de DiamondCMS - Forum", 
"DiamondCMS est livré avec un forum. Toutefois, il est possible de le désactiver, de le paramètrer et de le remplacer un forum externe.");

//On vérifie que TinyMCE est installé correctement
$conf_mce = cleanIniTypes(parse_ini_file(ROOT . "config/tinymce.ini", true));
if (($conf_mce['editor']['key'] == $conf_mce['editor']['def_key'] OR empty($conf_mce['editor']['key'])) && $conf_mce['editor']['enable']){
    $MCEAlert = $tb->AdminAlert("danger", "TinyMCE (Editeur de texte) n'est pas correctement configuré", "Vous devez suivre les étapes d'installation de ce dernier sur <a href=\"https://github.com/Aldric-L/DiamondCMS/wiki/Editeur-de-texte-TinyMCE\">ce guide</a> pour bénéficier d'un fonctionnement optimal du forum.", true);
    $adminBuilder->addAlert("col-lg-12", $MCEAlert);
}
// Partie droite de la page

$cats_available = array();
$catlist = $tb->AdminList();
$cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_cat", "*");
foreach($cats as $k => &$c){
    $c['scats'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_sous_cat", "*", array(array("id_cat", "=", $cats[$k]['id'])));
    $c['nb'] = 0;
    foreach ($c['scats'] as $sc){
        $c['nb'] += sizeof(simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum", "id_scat", array(array("id_scat", "=", $sc['id']))));
    } 
    array_push($cats_available, array(
        "val" => $c['id'],
        "disp" => $c['titre']
    ));
    $catlist->addField(
        $tb->UIString("<strong>" . $c['titre'] . "</strong> <small>(" . $c['nb'] ." sujets enregistrés à l'intérieur)</small>"), 
        $tb->AdminAPIButton("Supprimer", "btn btn-danger btn-sm", LINK . "api/", "forum", "set", "delcat", "id=" . (string)$c['id']));
}
unset($c);
$catpanel = $tb->AdminPanel("Catégories enregistrées", "fa-list", $catlist , "col-lg-8");

$scatlist = $tb->UIArray();

foreach ($cats as $c){
    $scatlist->push($tb->UIString("<h5><strong>Catégorie : </strong>" . $c['titre'] . "</h5>"));
    $sclist = $tb->AdminList();
    foreach ($c['scats'] as $sc){
        if ($sc['id_cat'] == $c['id']){
            $moovebutton = $tb->AdminDropdownButton($tb->AdminButton("Déplacer", "btn btn-sm btn-secondary"));
            foreach ($cats as $c_forbtn){
                if ($c_forbtn["id"] != $c['id']){
                    $moovebutton->addSubButton(
                        $tb->AdminAPIButton($c_forbtn['titre'], "", LINK . "api/", "forum", "set", "moovescat", "scat_id=" . (string)$sc['id'] . "&cat_id=" . (string)$c_forbtn['id']));
                }
            }
            $sclist->addField(
                $tb->UIString("<strong>" . $sc['titre'] . "</strong> <small>(" . $sc['nb_sujets'] ." sujets enregistrés à l'intérieur)</small>"), 
                $tb->UIArray($moovebutton,
                    $tb->AdminAPIButton("Supprimer", "btn btn-danger btn-sm", LINK . "api/", "forum", "set", "delscat", "id=" . (string)$sc['id'])));
        }
    }
    $scatlist->push($sclist);
    $scatlist->push($tb->UIString("<br>"));
}

$scatpanel = $tb->AdminPanel("Sous-catégories enregistrées", "fa-list", $scatlist , "col-lg-8");

// Partie gauche

$editform = $tb->AdminForm("editpage", false);
$editform->addCheckField("en_forum", "Activer le forum par défaut", $Serveur_Config['en_forum'])
         ->addCustom($tb->UIString("<hr><em>Pour modifier les réglages qui suivent, désactivez d'abord le forum par défaut.</em>"))
         ->addCheckField("other_forum", "Activer un forum externe", (isset($Serveur_Config["link_forum"]) && !empty($Serveur_Config["link_forum"])) ? $Serveur_Config["other_forum"] : "", false, new PageBuilders\AvailableIf($editform, "en_forum", PageBuilders\AvailableIf::EQUAL, false))
         ->addtextField("link_forum", "Lien le forum externe", (isset($Serveur_Config["link_forum"]) && !empty($Serveur_Config["link_forum"])) ? $Serveur_Config["link_forum"] : "", false, new PageBuilders\AvailableIf($editform, "other_forum", PageBuilders\AvailableIf::EQUAL, true));
$editpagepanel = $tb->AdminPanel("Modifier la page", "fa-pencil", $editform, "col-lg-4");

$onlyIfDefaultForum = new PageBuilders\AvailableIf($editform, "en_forum", PageBuilders\AvailableIf::EQUAL, true);
$nospecial = "Aucun caractère spécial ne doit figurer dans ce champ puisque celui-ci est utilisé dans l'url du forum. Ils seront automatiquement supprimés.";
$filter = getClearStringFilter(false, true);
$filter = array_merge($filter, array("_" => ""));

$catform = $tb->AdminForm("newCat", false)
        ->addTextField("titre_cat", "Nouvelle catégorie :", "", false, $onlyIfDefaultForum);
$catform->addAPIButton($tb->AdminAPIButton("Ajouter", "btn-sm btn btn-custom", LINK . "api/", "forum", "set", "newcat", $catform, "", true))
        ->setButtonsLine('class="text-right"');

$souscatform = $tb->AdminForm("newSousCat", false)
        ->addTextField("titre_scat", "Nouvelle sous-catégorie :", "", false, $onlyIfDefaultForum, $nospecial)
        ->addSelectField("cat_id", "Catégorie de rattachement :", $cats_available, false, $onlyIfDefaultForum)
        ->setFilter("titre_scat", $filter);
$souscatform->addAPIButton($tb->AdminAPIButton("Ajouter", "btn-sm btn btn-custom", LINK . "api/", "forum", "set", "newscat", $souscatform, "", true))
        ->setButtonsLine('class="text-right"');


$newcatandsouscatpanel = $tb->AdminPanel("Ajouter des catégories", "fa-plus", $tb->UIArray($catform, $tb->UIString("<hr>"), $souscatform), "col-lg-4");


$adminBuilder->addColumn($tb->UIColumn("col-lg-4", array($editpagepanel, $newcatandsouscatpanel)));
$adminBuilder->addColumn($tb->UIColumn("col-lg-8", array($catpanel, $scatpanel)));
echo $adminBuilder->render();