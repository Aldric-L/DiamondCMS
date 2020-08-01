<?php 

/** @deprecated Il convient désomais d'utiliser les fonctions de simplication du namespace simplifySQL */
function delNews($db, $id){
  return $db->exec("DELETE FROM d_news WHERE id = " . $id);
}

/**
 * addNews - Fonction pour poster une news, une fois que le fichier est uploadé
 * @author Aldric.L
 * @copyright Copyright 2020 Aldric L.
 * @deprecated Il convient désomais d'utiliser les fonctions de simplication du namespace simplifySQL
 * @return void
 */
 function addNews($db, $name, $content, $file_name, $user){
  $req = $db->prepare('INSERT INTO d_news (name, content_new, date, img, user) VALUES(:name, :content_new, :date, :img, :user)');
  $req->execute(array(
    'name' => $name,
    'content_new' => $content,
    //On récupère la date avec la fonction date
    'date' => date("Y-m-d"),
    'img' => $file_name,
    'user' => $user
  ));
}