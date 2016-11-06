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
  if (isset($_COOKIE['pseudo'])){
    unset($_COOKIE['pseudo']);
  }
}

/**
 * getInfo - Fonction pour recupérer des informationssur l'utilisateur
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return array
 */
function getInfo($db, $pseudo, $limite){
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
