<?php 
$controleur_def->loadModel('admin/news');

if (isset($param[2]) && !empty($param[2]) && $param[2] == "del_news_from_modal" && isset($param[3]) && !empty($param[3])){
    if (delNews($controleur_def->bddConnexion(), $param[3])){
        die("Success");
    }

    $controleur_def->addError(341);
}

$news = select($controleur_def->bddConnexion(), false, "d_news", "*", false, "date", true);

$controleur_def->loadViewAdmin('admin/news', 'accueil', 'Systeme de News');