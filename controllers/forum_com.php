<?php
$controleur_def->loadModel('forum/forum');

//Partie réponse :
if (!empty($_POST['comment'])){
  $newcom = newCom($controleur_def->bddConnexion(), $param[2], $_SESSION['pseudo'], $_POST['comment']);
}else {
  $erreurcom = "Vous devez écrire un message !";
}
//END REPONSE

//On parcourt récupère le post
$post = getPost($controleur_def->bddConnexion(), $param[2], 0, 10);

//On met en forme les donnés récupérés du getPost :
foreach ($post as $key => $post) {
  $posts[$key]['id'] = $post['id'];
  //On utilise la fonction htmlspecialchars pour eviter les failles de sécurité
  $posts[$key]['titre_post'] = htmlspecialchars($post['titre_post']);
  $posts[$key]['content_post'] = htmlspecialchars($post['content_post']);
}

//On cherche si le sujet est résolu :
if (is_solved($controleur_def->bddConnexion(), $post['id'])){
  $resolu = true;
}else {
  $resolu = false;
}

//On parcourt récupère les commentaires/réponses associés au post précédent
$coms = getComs($controleur_def->bddConnexion(), $post['id'], 0, 10);

//On met en forme les donnée du getCom
foreach ($coms as $key => $com) {
  $coms[$key]['user'] = $com['user'];
  $coms[$key]['date_com'] = $com['date_com'];
  $coms[$key]['content_com'] = htmlspecialchars($com['content_com']);
}

$controleur_def->loadView('pages/forum/forum_com', 'forum', 'Forum - ' . $post['titre_post']);
