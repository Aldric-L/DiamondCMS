<?php
/**
 * disconnect - Fonction pour se deconnecter
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return void
 * @access public
 */
function disconnect(){
  //On detruit la session
  session_destroy();
  //Si il y a un cookie de connexion, on le détruit.
  if (isset($_COOKIE['classe'])){
    unset($_COOKIE['classe']);
    setcookie('classe', '', 0);
  }
}

/**
 * getInfo - Fonction pour recupérer des informations sur l'utilisateur
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getInfo($db, $pseudo){
    $req = $db->prepare('SELECT id, pseudo, email, password, is_ban, money, DATE_FORMAT(date_last_vote, \'%d/%m/%Y à %Hh:%imin\') AS date_last_vote, votes, DATE_FORMAT(date_inscription, \'%d/%m/%Y à %Hh:%imin\') AS date_inscription, admin, role FROM d_membre WHERE pseudo = "' . $pseudo . '"');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $infos = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $infos;
}

/**
 * getLastActions - Fonction pour recupérer les actions d'un utilisateur
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getLastActions($db, $user, $min, $limite){
    $req2 = $db->prepare('SELECT id, content_com, user, DATE_FORMAT(date_comment, \'%d/%m/%Y\ à %Hh:%imin\') AS date_com, id_post FROM d_forum_com WHERE user = :user ORDER BY date_comment LIMIT :min, :limite ');

    //On passe les paramètres
    $req2->bindParam(':user', $user, PDO::PARAM_INT);
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
 * getPost - Fonction pour recupérer un post
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getPost($db, $id_post){
    $req = $db->prepare('SELECT id, titre_post, user FROM d_forum WHERE id = :id_post ORDER BY date_post');

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
 * ban - Fonction pour bannir un utilisateur
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return true (boolean)
 */
function ban($db, $pseudo, $r=null){
    if (isset($r) && !empty($r)){
        $db->exec("UPDATE d_membre SET is_ban = 1, r_ban = \"$r\" WHERE pseudo = \"$pseudo\"");   
    }else {
        $db->exec("UPDATE d_membre SET is_ban = 1 WHERE pseudo = \"$pseudo\"");   
    }
    return true;
}

/**
 * suppAll - Fonction pour supprimer tous les contenus d'un utilisateur
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return true (boolean)
 */
function suppAll($db, $pseudo){
    // Suppression de ses commentaires sur le forum
    $db->exec("DELETE * FROM d_forum_com WHERE user = \"$pseudo\"");
    // Suppression de ses sujets sur le forum
    $db->exec("DELETE * FROM d_forum WHERE user = \"$pseudo\"");
    // Supression de ses réponses dans le support
    $db->exec("DELETE * FROM d_support_rep WHERE pseudo = \"$pseudo\"");
    // Suppression de ses tickets support
    $db->exec("DELETE * FROM d_support_tickets WHERE pseudo = \"$pseudo\"");
    return true;
}