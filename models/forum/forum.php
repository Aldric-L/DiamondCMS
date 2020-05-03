<?php
/**
 *
 * Ces fonctions sont quasiment toutes dépréciées :
 * Désormais, il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @deprecated 
 * Elles seront donc supprimées d'ici une prochaine mise à jour. (Dernière édition - avril 2020)
 */



/**
 * getPosts - Fonction pour recupérer les derniers posts
 * @author Aldric.L
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getPosts($db, $id_scat, $min, $limite){
    $req = $db->prepare('SELECT id, titre_post, user, resolu, content_post, DATE_FORMAT(date_post, \'%d/%m/%Y\') AS date_p, id_scat, nb_rep FROM d_forum WHERE id_scat = ' . $id_scat . ' ORDER BY date_post DESC LIMIT :min, :limite ');

    //On passe les paramètres
    $req->bindParam(':min', $min, PDO::PARAM_INT);
    $req->bindParam(':limite', $limite, PDO::PARAM_INT);

    //On execute la requete
    $req->execute();
    //On récupère tout
    $post = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();
    foreach ($post as $k => $p){
      $post[$k]['date_post'] = $post[$k]['date_p'];
    }
    
    return $post;
}

/**
 * getNPosts - Fonction pour recupérer le nombre de posts
 * @author Aldric.L
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @copyright Copyright 2016-2017 Aldric L.
 * @return int
 */
function getNPosts($db, $id_scat){
    $req = $db->prepare('SELECT id, titre_post, user, resolu, nb_rep, content_post, DATE_FORMAT(date_post, \'%d/%m/%Y\') AS date_post, id_scat FROM d_forum WHERE id_scat = ' . $id_scat . ' ORDER BY id DESC');


    //On execute la requete
    $req->execute();
    //On récupère tout
    $post = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return sizeof($post);
}
    

/**
 * getPost - Fonction pour recupérer un post
 * @author Aldric.L
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getPost($db, $id_post){
    $req = $db->prepare('SELECT id, titre_post, user, resolu, nb_rep, content_post, id_scat, DATE_FORMAT(date_post, \'%d/%m/%Y\') AS date_post FROM d_forum WHERE id = :id_post ORDER BY date_post');

    //On passe les paramètres
    $req->bindParam(':id_post', $id_post, PDO::PARAM_INT);

    //On execute la requete
    $req->execute();
    //On récupère tout
    $post = $req->fetch();
    //On ferme la requete
    $req->closeCursor();

    return $post;
}

/**
 * getComs - Fonction pour recupérer les commentaires par post
 * @author Aldric.L
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getComs($db, $id_post){
    $req2 = $db->prepare('SELECT id, content_com, user, admin, DATE_FORMAT(date_comment, \'%d/%m/%Y\ à %Hh:%imin\') AS date_com FROM d_forum_com WHERE id_post = :id_post ORDER BY date_comment');

    //On passe les paramètres
    $req2->bindParam(':id_post', $id_post, PDO::PARAM_INT);

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
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @copyright Copyright 2016-2017 Aldric L.
 * @return void
 */
function newCom($db, $id_post, $pseudo, $content){
  $req = $db->prepare('INSERT INTO d_forum_com (id_post, content_com, user, date_comment, admin) VALUES(:id_post, :content_com, :user, :date_comment, :admin)');
  if (isset($_SESSION['admin']) && $_SESSION['admin'] == true){
    $req->execute(array(
      'id_post' => $id_post,
      'content_com' => $content,
      'user' => $pseudo,
      //On récupère la date avec la fonction date
      'date_comment' => date("Y-m-d H:i:s"),
      'admin' => 1
    ));
  }else {
    $req->execute(array(
      'id_post' => $id_post,
      'content_com' => $content,
      'user' => $pseudo,
      //On récupère la date avec la fonction date
      'date_comment' => date("Y-m-d H:i:s"),
      'admin' => 0
    ));
  }
  $select = simplifySQL\select($db, true, "d_forum", array("id", "nb_rep"), array("id", "=", $id_post));
  //var_dump($select);
  if (!empty($select)){
    $req = $db->exec('UPDATE d_forum SET nb_rep = ' . ($select["nb_rep"]+1) . ' WHERE id=' . $select["id"]);
  }
}

/**
 * newPost - Fonction pour poster un sujet
 * @author Aldric.L
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @copyright Copyright 2016-2017 Aldric L.
 * @return void
 */
function newPost($db, $title, $pseudo, $content, $scat){
  var_dump($db, $title, $pseudo, $content, $scat);
  //Pour mettre a jour le nombre de post de la sous_cat
  $select = $db->query('SELECT * FROM d_forum WHERE id_scat=' . $scat . '');
  $rep = $select->fetch();
  //Ensuite je modifie le nombre de sujets avec la valeur de select à laquelle j'ajoute 1 (nouveau post)
  $db->exec('UPDATE d_forum_sous_cat SET nb_sujets =' . ($rep['id']+1) .' WHERE id ='. $scat . '');

  $req = $db->prepare('INSERT INTO d_forum (titre_post, user, resolu, content_post, date_post, id_scat) VALUES(:titre_post, :user, :resolu, :content_post, :date_post, :id_scat)');
  $req->execute(array(
    'titre_post' => $title,
    'user' => $pseudo,
    'resolu' => 0,
    'content_post' => $content,
    //On récupère la date avec la fonction date
    'date_post' => date("Y-m-d H:i:s"),
    'id_scat' => $scat
  ));
}

/**
 * set_solved - Fonction pour definir l'état du sujet (résolu)
 * @author Aldric.L
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
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
    $rep = simplifySQL\select($db, true, "d_forum", "resolu", array(array("id", "=", $id_post)));
    if ($rep['resolu'] == 1){
      return true;
    }else {
      return false;
    }
  }

  /**
   * delSubject - Fonction pour supprimer un sujet
   * @author Aldric.L
   * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
   * @copyright Copyright 2016-2017 Aldric L.
   */
  function delSubject($db, $id){
    //Je récupère les infos de l'ancien post (scat, ...)
    $rep_s = simplifySQL\select($db, true, "d_forum", "*", array(array("id", "=", $id)));
    
    //Je le supprime
    $req = $db->prepare('DELETE FROM d_forum WHERE id = :id');
    $test = $req->execute(array(
      'id' => intval($id)
    ));
    //Je supprime les commentaires
    $req = $db->prepare('DELETE FROM d_forum_com WHERE id_post = :id');
    $test = $req->execute(array(
      'id' => intval($id)
    ));
    //Je met a jour le nombre de post de la sous_cat
    $select = $db->query('SELECT * FROM d_forum WHERE id_scat=' . $rep_s['id_scat'] . '');
    $rep = $select->fetch();
    //Ensuite je modifie le nombre de sujets avec la valeur de select à laquelle je retire 1 (ancien post)
    $db->exec('UPDATE d_forum_sous_cat SET nb_sujets =' . ($rep['id']-1) .' WHERE id ='. $rep_s['id_scat'] . '');

  }

  function delCom($db, $id){
    $com_post = simplifySQL\select($db, true, "d_forum_com", "*", array(array("id", "=", $id)));
    $sujet = simplifySQL\select($db, true, "d_forum", "*", array(array("id", "=", $com_post["id_post"])));

    if (!isset($com_post) || empty($com_post) || $com_post == null || $com_post = false){
      return false;
    }
    if ((isset($_SESSION['admin']) && $_SESSION['admin'] == true) || (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $com_post['user'])){
      $req = $db->prepare('DELETE FROM d_forum_com WHERE id = :id');
      $test = $req->execute(array(
        'id' => intval($id)
      ));
      $db->exec('UPDATE d_forum SET nb_rep = ' . ($sujet["nb_rep"]-1) . ' WHERE id = '. $sujet["id"]);
      return true;
    }else {
      return false;
    }
  }

  /**
   * getIdPost - Fonction pour recupérer les ID des posts
   * @author Aldric.L
   * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
   * @copyright Copyright 2016-2017 Aldric L.
   * @return array
   */
  function getIdPost($db){
      $req = $db->prepare('SELECT id, user FROM d_forum ORDER BY date_post');

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
