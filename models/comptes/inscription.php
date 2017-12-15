<?php
/**
 * addMembre - Fonction pour ajouter un membre
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return boolean
 * @access public
 */
function addMembre($db, $pseudo, $email, $news, $psw, $psw2){
  //Si les deux mots de passe sont bien égaux
  if ($psw == $psw2){
    //Si le mot de passe fait bien plus de 6 caractères
    if (strlen($psw) >= 6){
      //On test si il n'y a pas de membres enregistrés
      $isMembre = isMembre($db, $pseudo, $email, $_SERVER["REMOTE_ADDR"]);
      if ($isMembre == false){
        $cryptpsw = sha1($psw);
        $req = $db->prepare('INSERT INTO d_membre (pseudo, email, password, news, date_inscription, ip) VALUES(:pseudo, :email, :password, :news, :date_inscription, :ip)');
        $req->execute(array(
          'pseudo' => $pseudo,
          'email' => $email,
          'password' => $cryptpsw,
          'news' => $news,
          'date_inscription' => date('Y-m-d H:i:s'),
          'ip' => $_SERVER["REMOTE_ADDR"]
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
function isMembre($db, $pseudo, $email, $ip){
  //On récupère tous les membres ayant le meme pseudo ou email ou la même ip
  $req = $db->prepare('SELECT id, pseudo, email FROM d_membre WHERE pseudo = :pseudo OR email = :email OR ip = :ip');

  //On passe les paramètres
  $req->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
  $req->bindParam(':email', $email, PDO::PARAM_STR);
  $req->bindParam(':ip', $ip, PDO::PARAM_STR);

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
