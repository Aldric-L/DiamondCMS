<?php
if ($param[0] == 'forum' && isset($param[1]) && $param[1] == 'com' && $param[2] != null){

  $controleur_def->loadModel('forum/forum');
  $controleur_def->loadModel('forum/forum_cat');

  //On parcourt récupère le post
  $post = getPost($controleur_def->bddConnexion(), $param[2]);

  //Partie réponse :
  if (!empty($_POST['comment'])){
    $content = $_POST['comment'];
    $newcom = newCom($controleur_def->bddConnexion(), $param[2], $_SESSION['pseudo'], $content);
    if ($_SESSION['pseudo'] != $post['user']){
      $controleur_def->notify('Une nouvelle réponse a été envoyé sur votre sujet "'. $post['titre_post'] . '"', $post['user'], 4, "Nouvelle Activité", $Serveur_Config['protocol'] . "://" . $_SERVER['HTTP_HOST'] . WEBROOT . "forum/com/" . $param[2]);
    }
  }else {
    $erreurcom = "Vous devez écrire un message !";
  }
  //END REPONSE

  if (empty($post)){
    header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . 'forum');
  }
  $sous_cat = getSousCategorie($controleur_def->bddConnexion(), intval($post['id_scat']));
  //On récupère le nom de la categorie
  $cat = getCategorie($controleur_def->bddConnexion(), str_replace('-', ' ', $sous_cat[0]['id_cat']));


  //On cherche si le sujet est résolu :
  if (is_solved($controleur_def->bddConnexion(), $post['id'])){
    $resolu = true;
  }else {
    $resolu = false;
  }

  //On parcourt récupère les commentaires/réponses associés au post précédent
  $coms = getComs($controleur_def->bddConnexion(), $post['id']);

  //On met en forme les donnée du getCom
  foreach ($coms as $key => $com) {
    $coms[$key]['user'] = $com['user'];
    $coms[$key]['date_com'] = $com['date_com'];
    $coms[$key]['content_com'] = $com['content_com'];
  }

$controleur_def->loadView('pages/forum/forum_com', 'forum', 'Forum - ' /*. $post_info['titre_post']*/);
}else {
  require('forum.php');
}
