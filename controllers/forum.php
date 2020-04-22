<?php
//On verifie que le forum est actif
if (!$Serveur_Config['en_forum']){
  //Si un autre forum est utilisé
  if ($Serveur_Config['other_forum']){
    //On redirige vers celui-ci
    header('Location: ' . $Serveur_Config['link_forum']);
  }else {
    //Sinon on charge la vue indiquant qu'aucun forum n'a été associé avec le CMS
    $controleur_def->loadView('pages/forum/forum_none', 'emptyServer', 'Aucun Forum disponible');
    exit();
  }
}
//On charge le model
$controleur_def->loadModel('forum/forum');
$controleur_def->loadModel('forum/forum_cat');
if (!isset($param[1]) || empty($param[1])){

  $cats = getCategories($controleur_def->bddConnexion());

  foreach ($cats as $key => $cat) {
    $cats[$key]['sous_cat'] = getSousCategorie($controleur_def->bddConnexion(), $cats[$key]['id']);
  }
  //On termine par charger la vue, comme ça les variables/modifications seront prisent en comptes.
  $controleur_def->loadView('pages/forum/forum', 'forum', 'Forum');
  exit();
//Si on passe en mode commentaire
}else if ($param[0] == 'forum' && isset($param[1]) && $param[1] == 'com' && $param[2] != null){
  require('forum_com.php');
  exit();
//Si on passe en mode suppression de commentaire
}else if (isset($param[1]) && !empty($param[1]) && $param[1] == "del" && isset($param[1]) && !empty($param[2]) && $param[2] == "com" && isset($param[3]) && !empty($param[3])){
  //On recréé une variable qui nous permettra d'utiliser is_int
  $id = intval($param[3]);
  if (is_int($id)){
    $del_com = delCom($controleur_def->bddConnexion(), $id);
  }
//Si on passe en mode suppression de sujet
}else if (isset($param[1]) && !empty($param[1]) && $param[1] == "del" && isset($param[2]) && !empty($param[2])){
  //On recréé une variable qui nous permettra d'utiliser is_int
  $test = intval($param[2]);
  if (is_int($test)){
    //On test si c'est bien le propriétaire du sujet
    $idposts = getIdPost($controleur_def->bddConnexion());

    //On parcourt le tableau retourner par getPosts
    foreach ($idposts as $key => $idpost) {
      //Si l'id correspond
      if ($idposts[$key]['id'] == $param[2]){
        //On verifie que l'user est bien un admin
        if (isset($_SESSION['user']) && $_SESSION['user']->getLevel() >= 3){
          //On appelle la fonction pour supprimer un sujet
          $del_post = delSubject($controleur_def->bddConnexion(), $test);
        }
      }
    }
  }
  //Dans tout les cas on redirige sur forum
  header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum');
//Si on passe en mode résolution
}else if (isset($param[1]) && !empty($param[1]) && $param[1] == "solved" && isset($param[2]) && !empty($param[2])){
  $intvalparam2 = intval($param[2]);
  if (is_int($intvalparam2)){
    //On test si c'est bien le propriétaire du sujet
    $idposts = getIdPost($controleur_def->bddConnexion());
    //On parcourt le tableau retourner par getPosts
    foreach ($idposts as $key => $idpost) {
      //Si l'id correspond
      if ($idposts[$key]['id'] == $param[2]){
        //On verifie que l'user est bien un admin
        $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo', array(array("id", "=", $idposts[$key]['user'])));
        $pseudo = $membre['pseudo'];
        if ((isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $pseudo) || (isset($_SESSION['user']) && $_SESSION['user']->getLevel())){
          set_solved($controleur_def->bddConnexion(), $intvalparam2);
        }
      }
    }
  }
  //On redirige vers forum
  header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum');
//Si on passe en mode Post par Sous-Categories
}else if (isset($param[1]) && !empty($param[1]) && !empty(getSousCategorieByName($controleur_def->bddConnexion(), str_replace('-', ' ',$param[1])))){
  $sous_cat = getSousCategorieByName($controleur_def->bddConnexion(), str_replace('-', ' ', $param[1]));
  //On récupère le nom de la categorie
  $cat = getCategorie($controleur_def->bddConnexion(), $sous_cat['id_cat']);
  //On compte le nombre de posts
  $n_post = getNPosts($controleur_def->bddConnexion(), $sous_cat['id']);
  //On défini la variable qui contiendra le nombre de page nécessaires
  $pages = 0;
  for ($i=$n_post; $i > 0; $i = $i-10){
    $pages++;
  }

  //Si on est sur une autre page que la première :
  if (isset($param[2]) && !empty($param[2])){
    //On crée une variable pour savoir notre numéro de page
    $cur_page = intval($param[2])+1;
    //On récupère la connexion à la base de donnée pour la fonction de récupération des articles
    $posts = getPosts($controleur_def->bddConnexion(), $sous_cat['id'], intval($param[2])*10, 10);
  }else {
    // sinon on charge les 10 premiers articles
    //On crée une variable pour savoir notre numéro de page
    $cur_page = 1;
    //On récupère la connexion à la base de donnée pour la fonction de récupération des articles
    $posts = getPosts($controleur_def->bddConnexion(), $sous_cat['id'], 0, 10);
  }

  //On parcourt le tableau retourné par getPosts
  foreach ($posts as $key => $post) {
    //On les met en forme
    $posts[$key]['id'] = $post['id'];
    $posts[$key]['titre_post'] = $post['titre_post'];
    $posts[$key]['content_post'] = substr($post['content_post'], 0, 70) . '...';
    $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo, profile_img', array(array("id", "=", $posts[$key]['user'])));
    $pseudo = $membre['pseudo'];
    if (empty($pseudo)){
      $posts[$key]['user'] = "Utilisateur inconnu";
      $posts[$key]['profile_img'] = "no_profile.png";
    }else {
      $posts[$key]['user'] = $pseudo;
      $posts[$key]['profile_img'] = $membre['profile_img'];
    }
  }
}


//Si on reçoit des informations dans la variable $_POST
if (isset($_POST) && !empty($_POST) && isset($_SESSION['user'])){
  //Si le formulaire a bien été rempli entierement
  if (isset($_POST['titre_post']) && !empty($_POST['titre_post']) && isset($_POST['content_post']) && !empty($_POST['content_post']) && isset($_POST['scat']) && !empty($_POST['scat'])){
    //On appelle la fonction pour créer un sujet
    $content = $_POST['content_post'];

    $new_post = newPost($controleur_def->bddConnexion(), $_POST['titre_post'], $_SESSION['user']->getId(), $content, $_POST['scat']);
    header("Location: ".$_SERVER['HTTP_REFERER']."");
  }else {
    //On créé une variable qui sera affichée dans la vue si il nous manque une info
    $erreur_newpost = "Merci de compléter tous les champs";
  }
}


//On termine par charger la vue, comme ça les variables/modifications seront prisent en comptes.
$controleur_def->loadView('pages/forum/forum_scat', 'forum', 'Forum');
