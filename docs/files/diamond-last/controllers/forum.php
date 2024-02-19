<?php
/**
 * Controleur routeur du forum :
 * il dispatch aux controleurs spécialisés du dossier forum/ ou envoie directement sur le forum externe.
 */

//On verifie que le forum est actif
if (!$Serveur_Config['en_forum']){
  //Si un autre forum est utilisé
  if ($Serveur_Config['other_forum']){
    //On redirige vers celui-ci
    header('Location: ' . $Serveur_Config['link_forum']);
  }else {
    $controleur_def->nonifyPage("Aucun forum disponible", "Le Forum a été desactivé par l'administrateur !");
    exit();
  }
}

$controleur_def->loadModel('forum');

if (isset($param[1]) && $param[1] == 'com' 
    && $param[2] != null && is_numeric($param[2])
    && is_array($post = getPost($controleur_def->bddConnexion(), $param[2]))){
  require('forum/post.forum.php');
  exit();
//Si on passe en mode Post par Sous-Categories
}else if (isset($param[1]) && !empty($param[1]) 
          && !empty($sous_cat=getSousCategorieByName($controleur_def->bddConnexion(), str_replace('_', ' ',$param[1])))){
  require_once("forum/posts-list.forum.php");
  die;
}
require_once("forum/categories.forum.php");
