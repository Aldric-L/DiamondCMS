<?php 
if (isset($param[1]) && !empty($param[1])){
    $news = select($controleur_def->bddConnexion(), true, "d_news", "*", array("id", "=", $param[1]), "date", true, false);
    
    $controleur_def->loadView('pages/news/view_news', '', 'News');
}else {
    $news = select($controleur_def->bddConnexion(), false, "d_news", "*", false, "date", true, false);
    
    $controleur_def->loadView('pages/news/news', '', 'News');
}
