<?php
/**
 * addMembre - Fonction pour ajouter un membre
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return int
 * @access public
 */
function addMembre($db, $pseudo, $email, $news, $psw, $psw2, $diamond_master=FALSE){
  //Si les deux mots de passe sont bien égaux
  if ($psw == $psw2){
    //Si le mot de passe fait bien plus de 6 caractères
    if (strlen($psw) >= 6){
      //On test si il n'y a pas de membres enregistrés
      $isMembre = isMembre($db, $pseudo, $email, $_SERVER["REMOTE_ADDR"]);
      if ($isMembre == false){
        $uuid = uniqid();
        $cryptpsw = sha1($uuid + $psw);
        if ($diamond_master){
          $req = $db->prepare('INSERT INTO d_membre (pseudo, email, password, salt, news, date_inscription, ip, profile_img, role) VALUES(:pseudo, :email, :password, :salt, :news, :date_inscription, :ip, :profile_img, :role)');
          $req->execute(array(
            'pseudo' => $pseudo,
            'email' => $email,
            'salt' => $uuid,
            'password' => $cryptpsw,
            'news' => $news,
            'date_inscription' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER["REMOTE_ADDR"],
            'profile_img' => "profiles/no_profile.png",
            'role' => 6
          ));
        }else {
          $req = $db->prepare('INSERT INTO d_membre (pseudo, email, password, salt, news, date_inscription, ip, profile_img) VALUES(:pseudo, :email, :password, :salt, :news, :date_inscription, :ip, :profile_img)');
          $req->execute(array(
            'pseudo' => $pseudo,
            'email' => $email,
            'salt' => $uuid,
            'password' => $cryptpsw,
            'news' => $news,
            'date_inscription' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER["REMOTE_ADDR"],
            'profile_img' => "profiles/no_profile.png"
          ));
        }
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
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @copyright Copyright 2016-2017 Aldric L.
 * @return boolean
 * @access private
 */
function isMembre($db, $pseudo, $email, $ip){
  //On récupère tous les membres ayant le meme pseudo ou email ou la même ip
  $req = $db->prepare('SELECT id, pseudo, email FROM d_membre WHERE pseudo = :pseudo OR email = :email');

  //On passe les paramètres
  $req->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
  $req->bindParam(':email', $email, PDO::PARAM_STR);

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
