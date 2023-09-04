<?php 
$cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_cat", "*");
foreach ($cats as $key => $cat) {
    $cats[$key]['sous_cat'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_sous_cat", "*", array(array("id_cat", "=", $cats[$key]['id'])));
}

$content_explic = file_get_contents(ROOT . "config/forum.ftxt");
if (!(isset($_SESSION['editing_mode']) && $_SESSION['editing_mode'])){
    foreach (TEXT_ALIAS as $key => $a){
        $content_explic = str_replace($key, $a, $content_explic);
    }
}
$controleur_def->loadView('pages/forum/forum', 'forum', 'Forum');