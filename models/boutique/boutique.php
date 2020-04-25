<?PHP
/**
 * getArticles - Fonction pour recupérer tous les articles 
 * @deprecated 2020
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getArticles($db){
    $req = $db->prepare('SELECT id, name, description, img, prix, cat, DATE_FORMAT(date_ajout, \'%d/%m/%Y\') AS date_add FROM d_boutique_articles ORDER BY date_ajout DESC');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $articles = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $articles;
}

/**
 * getReviewsByid - Fonction pour recupérer les avis sur un article avec son identifiant
 * @deprecated 2020
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
 function getReviewsByid($db, $id){
    $req = $db->prepare('SELECT * FROM d_boutique_avis WHERE id_article="' . $id . '" ORDER BY date DESC');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $articles = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $articles;
}

/**
 * getArticleByid - Fonction pour recupérer un article avec son identifiant unique
 * @deprecated 2020
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
 function getArticleByid($db, $id){
    $req = $db->prepare('SELECT id, name, description, img, prix, cat, DATE_FORMAT(date_ajout, \'%d/%m/%Y\') AS date_add FROM d_boutique_articles WHERE id="' . $id . '" ORDER BY date_ajout DESC');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $articles = $req->fetch();
    //On ferme la requete
    $req->closeCursor();

    return $articles;
}

/**
 * getArticlesByCat - Fonction pour recupérer tous les articles par la catégorie 
 * @deprecated 2020
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getArticlesByCat($db, $cat){
    $req = $db->prepare('SELECT id, name, description, img, prix, cat, DATE_FORMAT(date_ajout, \'%d/%m/%Y\') AS date_add FROM d_boutique_articles  WHERE cat='. $cat .' ORDER BY date_ajout DESC');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $articles = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $articles;
}

/**
 * getLastArticles - Fonction pour recupérer les 6 derniers articles
 * @deprecated 2020
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getLastArticles($db){
    $req = $db->prepare('SELECT id, name, description, img, prix, cat, DATE_FORMAT(date_ajout, \'%d/%m/%Y\') AS date_add FROM d_boutique_articles ORDER BY date_ajout DESC LIMIT 6 ');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $articles = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $articles;
}

/**
 * getCat - Fonction pour recupérer une catégorie par son id
 * @deprecated 2020
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getCat($db, $id){
    $req = $db->prepare('SELECT name FROM d_boutique_cat WHERE id=' . $id);

    //On execute la requete
    $req->execute();
    //On récupère tout
    $cat = $req->fetch();
    //On ferme la requete
    $req->closeCursor();

    return $cat;
}

/**
 * getAllCats - Fonction pour recupérer toutes les catégories
 * @deprecated 2020
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getAllCats($db){
    $req = $db->prepare('SELECT * FROM d_boutique_cat');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $cat = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $cat;
}
