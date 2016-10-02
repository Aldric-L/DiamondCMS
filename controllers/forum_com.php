<?php
//Ce fichier est appelé lors de la demande de l'affichage des réponses
$controleur_def = new Controleur($Serveur_Config);

//On charge le model pour le vote...
$controleur_def->loadModel('vote');

//On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
$voteurs = bestVotes($controleur_def->bddConnexion());

$controleur_def->loadModel('forum/forum');

//On parcourt récupère le post
$post = getPost($controleur_def->bddConnexion(), $param[2], 0, 10);

//On parcourt récupère les commentaires associer au post précédent
$coms = getComs($controleur_def->bddConnexion(), $post[0]['id'], 0, 10);

//On met en forme les donnée du getCom
foreach ($coms as $key => $com) {
  $coms[$key]['user'] = $com['user'];
  $coms[$key]['date_com'] = $com['date_com'];
  $coms[$key]['content_com'] = htmlspecialchars($com['content_com']);
}

foreach ($post as $key => $post) {
  //On utilise le fonction htmlspecialchars pour eviter les failles de sécurités
  $posts[$key]['titre_post'] = htmlspecialchars($post['titre_post']);
  $posts[$key]['content_post'] = htmlspecialchars($post['content_post']);
}

$controleur_def->loadView('pages/forum/forum_com', 'forum', 'Forum - ' . $post['titre_post']);
