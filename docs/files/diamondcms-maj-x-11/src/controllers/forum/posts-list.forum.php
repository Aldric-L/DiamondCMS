<?php 

//On récupère le nom de la categorie
$cat = simplifySQL\select($controleur_def->bddConnexion(), true, "d_forum_cat", array("id", "titre"), array(array("id", "=", $sous_cat['id_cat'])), "id");
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

$controleur_def->loadView('pages/forum/forum_scat', 'forum', 'Forum');