<?php
/**
 * addMembre - Fonction pour ajouter un membre
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return boolean
 * @access public
 */
function addMembre($db, $pseudo, $email, $news, $psw, $psw2){
  if ($psw == $psw2){
    if (strlen($psw) >= 6){
      $isMembre = isMembre($db, $pseudo);
      if ($isMembre == false){
        $cryptpsw = sha1($psw);
        $req = $db->prepare('INSERT INTO d_membre (pseudo, email, password, news, date_inscription) VALUES(:pseudo, :email, :password, :news, :date_inscription)');
        $req->execute(array(
          'pseudo' => $pseudo,
          'email' => $email,
          'password' => $cryptpsw,
          'news' => $news,
          'date_inscription' => date()
        ));
      }else {
        return 3;
      }
    }else {
      return 2;
    }
  }else {
    return 1;
  }
}

/**
 * isMembre - Fonction pour savoir si un pseudo existe déja
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return boolean
 * @access private
 */
function isMembre($db, $pseudo){
  //On récupère tous les membres ayant le meme pseudo
  $req = $db->prepare('SELECT id, pseudo FROM d_membre WHERE pseudo = :pseudo');

  //On passe les paramètres
  $req->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);

  //On execute la requete
  $req->execute();
  //On récupère tout
  $rep = $req->fetchAll();
  //On ferme la requete
  $req->closeCursor();

  //si on trouve un membre, on retourne true
  if ($rep != null){
    return true;
  }
  return false;
}
