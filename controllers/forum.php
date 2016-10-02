<?php
//On instancie le controleur en lui passant l'acces aux fichiers config
$controleur_def = new Controleur($Serveur_Config);

//On charge le model pour le vote...
$controleur_def->loadModel('vote');

//On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
$voteurs = bestVotes($controleur_def->bddConnexion());

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
  //On utilise le fonction htmlspecialchars pour eviter les failles de sécurités
  $posts[$key]['titre_post'] = htmlspecialchars($post['titre_post']);
  $posts[$key]['content_post'] = substr(htmlspecialchars($post['content_post']), 0, 70) . '...';
}

//On charge la vue, la fonction va charger 3 fichiers.
$controleur_def->loadView('pages/forum/forum', 'forum', 'Forum');
