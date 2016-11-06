<?php
//On charge le model
$controleur_def->loadModel('forum/forum');

//On récupère la connexion à la base de donnée pour la fonction de récupération des articles
$posts = getPosts($controleur_def->bddConnexion(), 0, 10);

//On parcourt le tableau retourner par getPosts
foreach ($posts as $key => $post) {
  //On les mets en forme
  $posts[$key]['last_user'] = ' | Dernière réponse par ' . $post['last_user'];
  $posts[$key]['date_last_post'] = ' le ' . $post['date_last_post'];
  $posts[$key]['id'] = $post['id'];
  //On utilise le fonction htmlspecialchars pour eviter les failles de sécurité
  $posts[$key]['titre_post'] = htmlspecialchars($post['titre_post']);
  $posts[$key]['content_post'] = substr(htmlspecialchars($post['content_post']), 0, 70) . '...';

  //Gestion des sujets résolus :
  //Appel de la fonction
  $solved = is_solved($controleur_def->bddConnexion(), $posts[$key]['id']);
  //Si le sujet est résolu
  if ($solved == true){
    $post['solved'] = true;
  }else {
    $post['solved'] = false;
  }
}

//On charge la vue, la fonction va charger 3 fichiers.
$controleur_def->loadView('pages/forum/forum', 'forum', 'Forum');
