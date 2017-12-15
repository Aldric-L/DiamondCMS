<?php 
$controleur_def->loadModel('boutique/boutique');

if (isset($param[1]) && !empty($param[1]) && $param[1] == "article" && isset($param[2]) && !empty($param[2]) && isset($param[3]) && !empty($param[3])){
    $article_name = str_replace("_", " ", $param[2]);
    $id = $param[3];
    $article = getArticleByid($controleur_def->bddConnexion(), $id);
    $reviews = getReviewsByid($controleur_def->bddConnexion(), $article['id']);
    $controleur_def->loadView('pages/boutique/boutique_article', 'boutique', $article_name);
    die;
}

$l_articles = getLastArticles($controleur_def->bddConnexion());
$n_articles_global = 0;
foreach ($l_articles as $key => $article){
    $cat = getCat($controleur_def->bddConnexion(), intval($article["cat"]));
    $article["cat"] = $cat['name'];
    $l_articles[$key]["cat"] = $article["cat"];
    $n_articles_global++;    
}
unset($cat, $key);
$cats = getAllCats($controleur_def->bddConnexion());
foreach ($cats as $key => $cat){
    $cat['articles'] = getArticlesByCat($controleur_def->bddConnexion(), intval($cat['id']));
    $cats[$key]['articles'] = $cat['articles'];
}


$controleur_def->loadView('pages/boutique/boutique', 'boutique', 'Boutique');