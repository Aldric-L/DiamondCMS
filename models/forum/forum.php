<?php
/**
 * getPosts - Fonction pour recupérer les derniers posts
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getPosts($db, $min, $limite){
    $req = $db->prepare('SELECT id, titre_post, user, last_user, resolu, content_post, DATE_FORMAT(date_last_post, \'%d/%m/%Y à %Hh:%imin\') AS date_last_post, DATE_FORMAT(date_post, \'%d/%m/%Y\') AS date_post FROM d_forum ORDER BY date_last_post LIMIT :min, :limite ');

    //On passe les paramètres
    $req->bindParam(':min', $min, PDO::PARAM_INT);
    $req->bindParam(':limite', $limite, PDO::PARAM_INT);

    //On execute la requete
    $req->execute();
    //On récupère tout
    $post = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $post;
}

/**
 * getPost - Fonction pour recupérer un post
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getPost($db, $id_post, $min, $limite){
    $req = $db->prepare('SELECT id, titre_post, user, last_user, resolu, content_post, DATE_FORMAT(date_last_post, \'%d/%m/%Y à %Hh:%imin\') AS date_last_post, DATE_FORMAT(date_post, \'%d/%m/%Y\') AS date_post FROM d_forum WHERE id = :id_post ORDER BY date_last_post');

    //On passe les paramètres
    $req->bindParam(':id_post', $id_post, PDO::PARAM_INT);

    //On execute la requete
    $req->execute();
    //On récupère tout
    $post = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $post;
}

/**
 * getComs - Fonction pour recupérer les commentaires par post
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getComs($db, $id_post, $min, $limite){
    $req2 = $db->prepare('SELECT id, content_com, user, DATE_FORMAT(date_com, \'%d/%m/%Y\ à %Hh:%imin\') AS date_com FROM d_forum_com WHERE id_post = :id_post ORDER BY date_com LIMIT :min, :limite ');

    //On passe les paramètres
    $req2->bindParam(':id_post', $id_post, PDO::PARAM_INT);
    $req2->bindParam(':min', $min, PDO::PARAM_INT);
    $req2->bindParam(':limite', $limite, PDO::PARAM_INT);

    //On execute la requete
    $req2->execute();
    //On récupère tout
    $com_post = $req2->fetchAll();
    //On ferme la requete
    $req2->closeCursor();

    return $com_post;
}

/**
 * newCom - Fonction pour poster un commentaire
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return void
 */
function newCom($db, $id_post, $pseudo, $content){
  $req = $db->prepare('INSERT INTO d_forum_com (id_post, content_com, user, date_com) VALUES(:id_post, :content_com, :user, :date_com)');
  $req->execute(array(
    'id_post' => $id_post,
    'content_com' => htmlspecialchars($content),
    'user' => $pseudo,
    //On récupère la date avec la fonction date
    'date_com' => date("Y-m-d H:i:s")
  ));
}

/**
 * set_solved - Fonction pour definir l'état du sujet (résolu)
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return void
 */
 function set_solved($db, $id_post){
   //On met à jour l'entrée "resolu" dans la BDD en 1
   $db->exec('UPDATE d_forum SET resolu =1 WHERE id = "' . $id_post . '"');
 }

 /**
  * is_solved - Fonction pour connaitre l'état du sujet (résolu)
  * @author Aldric.L
  * @copyright Copyright 2016-2017 Aldric L.
  * @return bool
  */
  function is_solved($db, $id_post){
    $select = $db->query('SELECT resolu FROM d_forum WHERE id = "' . $id_post . '"');
    $rep = $select->fetch();
    if ($rep['resolu'] == 1){
      return true;
    }else {
      return false;
    }
  }
