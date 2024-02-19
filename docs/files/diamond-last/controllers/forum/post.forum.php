<?php
$membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo', array(array("id", "=", $post['user'])));
$post['user_id'] = $post['user'];
$post['user'] = ($membre == false || !is_array($membre) || !array_key_exists('pseudo', $membre) || empty($membre['pseudo'])) ? "Utilisateur inconnu" : $membre['pseudo'];
  
$sous_cat = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_sous_cat", "*", array(array("id", "=", $post['id_scat'])));
$cat = simplifySQL\select($controleur_def->bddConnexion(), true, "d_forum_cat", array("id", "titre"), array(array("id", "=", $sous_cat[0]['id_cat'])), "id");
$resolu = is_solved($controleur_def->bddConnexion(), $post['id']);
$coms = getComs($controleur_def->bddConnexion(), $post['id']);

//On met en forme les donnée du getCom
foreach ($coms as $key => $com) {
    $membre = simplifySQL\select($controleur_def->bddconnexion(), true, "d_membre", 'pseudo', array(array("id", "=", $coms[$key]['user'])));
    $coms[$key]['user_id'] = $coms[$key]['user'];
    if ($membre == false || !is_array($membre) || !array_key_exists('pseudo', $membre) || empty($membre['pseudo'])){
      $coms[$key]['user'] = "Utilisateur inconnu";
      $coms[$key]['role'] = "";
    }else {
      $coms[$key]['user'] = $membre['pseudo'];
      $coms[$key]['role'] = $controleur_def->echoRoleName($controleur_def->bddConnexion(), $coms[$key]['user']);
    }
}

$scats = simplifySQL\select($controleur_def->bddConnexion(), false, "d_forum_sous_cat", "*");

$controleur_def->loadJS("forum_com");
$controleur_def->loadView('pages/forum/forum_com', 'forum', 'Forum - ' . $post['titre_post']);