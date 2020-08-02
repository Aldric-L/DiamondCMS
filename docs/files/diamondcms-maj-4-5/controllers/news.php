<?php 
if (isset($param[1]) && !empty($param[1])){
    $news = simplifySQL\select($controleur_def->bddConnexion(), true, "d_news", "*", array("id", "=", $param[1]), "date", true, false);
    $news['user'] = $controleur_def->getPseudo($news['user']);
    $controleur_def->loadView('pages/news/view_news', '', 'News');
}else {
    $news = simplifySQL\select($controleur_def->bddConnexion(), false, "d_news", "*", false, "date", true, false);
    foreach ($news as $k => $n){
        $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo, profile_img', array(array("id", "=", $news[$k]['user'])));
        if (!empty($membre)){
            $news[$k]['user'] = $membre['pseudo'];
            $news[$k]['img_profile'] = $membre['profile_img'];
        }else {
            $news[$k]['user'] = "Utiisateur inconnu";
            $news[$k]['img_profile'] ="no_profile.png";
        }
    }
    $controleur_def->loadView('pages/news/news', '', 'News');
}
