<?php
/**
 * disconnect - Fonction pour se deconnecter
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L. 2020
 * @return void
 * @access public
 */
function disconnect(){
  //On detruit la session
  $_SESSION = array();
  if (isset($_COOKIE['pseudo'])){
      
    setcookie('pseudo', "", time(), WEBROOT, $_SERVER['HTTP_HOST'], false, true);
    //setcookie("pseudo", "", time() - 3600);
    //unset($_COOKIE['pseudo']);
  }
  var_dump($_COOKIE, isset($_COOKIE['pseudo'])); //die;
}


/**
 *
 * Ces fonctions sont quasiment toutes dépréciées :
 * Désormais, il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @deprecated 
 * Elles seront donc supprimées d'ici une prochaine mise à jour. (Dernière édition - avril 2020)
 */

/**
 * getInfo - Fonction pour recupérer des informations sur l'utilisateur
 * @author Aldric.L
 * @deprecated 
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getInfo($db, $pseudo){
    $req = $db->prepare('SELECT id, pseudo, email, password, is_ban, money, DATE_FORMAT(date_last_vote, \'%d/%m/%Y à %Hh:%imin\') AS date_last_vote, votes, DATE_FORMAT(date_inscription, \'%d/%m/%Y à %Hh:%imin\') AS date_inscription, role FROM d_membre WHERE pseudo = "' . $pseudo . '"');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $infos = $req->fetchAll();
    //On ferme la requete
    $req->closeCursor();

    return $infos;
}

/**
 * getPost - Fonction pour recupérer un post
 * @author Aldric.L
 * @deprecated 
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
 * @deprecated 
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
 * ban - Fonction pour bannir un utilisateur
 * @author Aldric.L
 * @deprecated 
 * @copyright Copyright 2016-2017 Aldric L.
 * @return true (boolean)
 */
 function banId($db, $id, $r=null){
    if (isset($r) && !empty($r)){
        $db->exec("UPDATE d_membre SET is_ban = 1, r_ban = \"$r\" WHERE id = \"$id\"");   
    }else {
        $db->exec("UPDATE d_membre SET is_ban = 1 WHERE id = \"$id\"");   
    }
    return true;
}
