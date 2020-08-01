<?php 
//On fait le choix ici de séparer les controlleurs pour que leur lecture reste aisée
//Si la page "boutique/getback" est demandée, alors on charge son controlleur
if (isset($param[1]) && !empty($param[1]) && $param[1] == "getback" && isset($param[2]) && !empty($param[2])){
    require_once (ROOT . "controllers/boutique.getback.php");
    die;
}

//Si la page "boutique/getmoney" est demandée, alors on charge son controlleur
if (isset($param[1]) && !empty($param[1]) && $param[1] == "getmoney"){
    require_once (ROOT . "controllers/boutique.getmoney.php");
    die;
}

//Si on passe en mode XHR (achat d'un article)
//Comme l'achat se passe en AJAX, cette page retourne des codes d'erreur sous la forme de nombres :
// 0 : erreur de paramètres
// 1 : pas assez d'argent disponible
// 2 : erreur dans l'insert de la transaction (argent pas encore prélevé)
// 3 : erreur dans le prélevement de l'argent (update de la table d_membre)
// 4 : erreur dans l'insertion des taches à réaliser
// 5 : on ne parvient pas à récupèrer l'id de la commande que l'on vient d'insérer dans la BDD (ça semble impossible !)
// 6 : on ne trouve pas le membre qui essaye de payer
if (isset($param[1]) && !empty($param[1]) && isset($param[2]) && !empty($param[2]) && isset($_SESSION['user']) && !empty($_SESSION['user'])){
    if ($param[1] == "buy"){
        //On commence par récupérer le prix de l'article
        $article = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "*", array(array("id", "=", $param[2])));
        if (empty($article) || !$article){
            die('0');
        }

        //Parce que l'objet $_SESSION['user'] n'est pas fiable, on fait le choix d'aller chercher le portefeuille vituel directement de la BDD
        $m = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "*", array(array("id", "=", $_SESSION['user']->getId())));
        if (empty($m)){
            die('6');
        }

        $prix = intval($article['prix']);
        $money = intval($m['money']);
        $newmoney = intval($money) - intval($prix);
        if ($money < 0 || $prix > $money){
            die('1');
        }

        //On définit un identifiant unique pour la transaction qui permettera de retrouver de manière certaine la transaction
        $uuid = uniqid();

        //On créé la transaction dans la base de données
        if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_achats", 
            array("id_user", "id_article", "uuid", "price",  "date", "success"),
            array($_SESSION['user']->getId(), $article['id'], $uuid, $prix, date("Y-m-d"), 0)
        )){
            die('2');
        }

        //On prélève l'argent
        if (!simplifySQL\update($controleur_def->bddConnexion(), "d_membre", 
            array(array("money", "=", $newmoney)),
            array( array( "id", "=", intval($_SESSION['user']->getId()) ) )
        )){
            die('3');
        }

        //On enregistre les tâches (les commandes à éxecuter sur les serveurs)
        if (defined("DServerLink") && DServerLink){
            $cmds = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cmd", "*", array( array("id_article", "=", $article['id']) ));
            //On récupère l'id de la commande réalisée
            $commande = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_achats", "*", array(array("uuid", "=", $uuid)));
            if (empty($commande)){
                die('5');
            }
            if (!empty($cmds)){
                foreach($cmds as $cmd){
                    if (!simplifySQL\insert($controleur_def->bddConnexion(), "d_boutique_todolist",
                        array("id_commande", "cmd", "done", "date_send"),
                        array($commande['id'], $cmd['id'], 0, date("Y-m-d H:i:s"))
                    )){
                        die('4');
                    }
                    if ($cmd['is_manual']){
                        $controleur_def->notify("Une nouvelle tâche manuelle est à réaliser ! (Un article a été acheté en boutique)", "admin", 5, 'Tâche manuelle', $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'admin/boutique/tasks/');
                    }
                }
            }
        }

        $_SESSION['user']->reload($controleur_def->bddConnexion());
        //Si tout est en ordre, on retourne une url, le lien vers la récupération du lot
        die ('url:' . $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'boutique/getback/' . $uuid . '/');
    }
}

//Si on veut afficher la page d'un article
if (isset($param[1]) && !empty($param[1]) && $param[1] == "article" && isset($param[2]) && !empty($param[2])){
    $id = $param[3];
    $article = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_articles", "*", array(array("id", "=", $param[2])));
    if (!empty($article)){
        $article_name = $article['name'];
        if (strpos($article['img'], "png") !== false) {
            $article['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/png/' . substr($article['img'], 0, -4) . '/300/300';
        }else if (strpos($article['img'], "jpg") !== false) {
            $article['link'] =  $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpg/' . substr($article['img'], 0, -4) . '/300/300';
        }else if (strpos($article['img'], "jpeg") !== false) { 
            $article['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpeg/' . substr($article['img'], 0, -5) . '/300/300';
        }

        $cant_by = 0;

        if (!isset($_SESSION['user']) || empty($_SESSION['user'])){
            $cant_by = 1;
        }else if ($_SESSION['user']->getMoney($controleur_def->bddConnexion()) < $article['prix']){
            $cant_by = 2;
        }

        $controleur_def->loadJS('boutique_article');
        $controleur_def->loadView('pages/boutique/boutique_article', 'boutique', $article_name);
    }
    die;
}

$l_articles = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_articles", "*", array(array("archive", "=", 0)), "id", true, array(0, 6));
$n_articles_global = 0;
foreach ($l_articles as $key => $article){
    $cat = simplifySQL\select($controleur_def->bddConnexion(), true, "d_boutique_cat", "*", array(array("id", "=", $article["cat"])));
    $article["cat"] = $cat['name'];
    $l_articles[$key]["cat"] = $article["cat"];
    if (strpos($l_articles[$key]['img'], "png") !== false) {
        $l_articles[$key]['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/png/' . substr( $l_articles[$key]['img'], 0, -4) . '/200/200';
    }else if (strpos( $l_articles[$key]['img'], "jpg") !== false) {
        $l_articles[$key]['link'] =  $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpg/' . substr( $l_articles[$key]['img'], 0, -4) . '/200/200';
    }else if (strpos( $l_articles[$key]['img'], "jpeg") !== false) { 
        $l_articles[$key]['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpeg/' . substr( $l_articles[$key]['img'], 0, -5) . '/200/200';
    }
    $n_articles_global++;    
}
unset($cat, $key);
$cats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_cat", "*");
foreach ($cats as $key => $cat){
    $cat['articles'] = simplifySQL\select($controleur_def->bddConnexion(), false, "d_boutique_articles", array('id', 'name', 'archive', 'description', 'img', 'prix', 'cat', array('date_ajout', "%d/%m/%Y", 'date_add')), array(array('cat', '=', $cat['id']), "AND", array("archive", "=", 0)), 'date_ajout', true);
    $cats[$key]['articles'] = $cat['articles'];
    foreach ($cats[$key]['articles'] as $k => $c){
        if (strpos($cats[$key]['articles'][$k]['img'], "png") !== false) {
            $cats[$key]['articles'][$k]['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/png/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/200/200';
        }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpg") !== false) {
            $cats[$key]['articles'][$k]['link'] =  $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -4) . '/200/200';
        }else if (strpos( $cats[$key]['articles'][$k]['img'], "jpeg") !== false) { 
            $cats[$key]['articles'][$k]['link'] = $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'getimage/jpeg/' . substr( $cats[$key]['articles'][$k]['img'], 0, -5) . '/200/200';
        }
    }
}


$controleur_def->loadView('pages/boutique/boutique', 'boutique', 'Boutique');