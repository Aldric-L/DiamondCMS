<?php
/**
 * addMembre - Fonction pour ajouter un membre (gérer l'inscription)
 * @author Aldric.L
 * @copyright Copyright 2016-2017 2022 Aldric L.
 * @return int -1 : mp pas égaux, -2 mp pas assez long, -3 pseudo déja utilisé, -4 même ip qu'un compte banni, -5 impossible de récupérer le rôle par défaut
 * @access public
 */

function addMembre($db, $pseudo, $email, $news, $psw, $psw2, $diamond_master=FALSE, $banip=false){
  //Si les deux mots de passe sont bien égaux
  if ($psw == $psw2){
    //Si le mot de passe fait bien plus de 6 caractères
    if (strlen($psw) >= 6){
      //On teste si il n'y a pas de membres enregistrés
      $test1 = simplifySQL\select($db, true, "d_membre", "*", array(array("pseudo", "=", $pseudo), "OR", array("email", "=", $email)));
      if ($test1 != false && !empty($test1))
        return -3;

      //On vérifie l'IP du compte banni
      if ($banip){
          $test2 = simplifySQL\select($db, true, "d_membre", "*", array(array("ip", "=", $_SERVER['REMOTE_ADDR']), "AND", array("is_ban", "=", true), "AND", array("date_lc_timestamp", ">", time()-60*60*24*30)));
          if ($test2 != false && !empty($test2)){
            return -4;
          }
      }
      
        $uuid = uniqid();
        $cryptpsw = sha1((string)$uuid . (string)$psw);
        if ($diamond_master){
          $req = $db->prepare('INSERT INTO d_membre (pseudo, email, password, salt, news, date_inscription, ip, profile_img, role) VALUES(:pseudo, :email, :password, :salt, :news, :date_inscription, :ip, :profile_img, :role)');
          $req->execute(array(
            'pseudo' => $pseudo,
            'email' => $email,
            'salt' => $uuid,
            'password' => $cryptpsw,
            'news' => intval($news),
            'date_inscription' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER["REMOTE_ADDR"],
            'profile_img' => "profiles/no_profile.png",
            'role' => 6
          ));
        }else {
          //On récupère le rôle par défaut
          $test3 = simplifySQL\select($db, true, "d_roles", "*", array(array("dflt", "=", true)));
          if ($test3 == false || empty($test3) || !isset($test3['id'])){
            return -5;
          }
          $role = $test3['id'];

          $req = $db->prepare('INSERT INTO d_membre (pseudo, email, password, salt, news, date_inscription, ip, profile_img, role) VALUES(:pseudo, :email, :password, :salt, :news, :date_inscription, :ip, :profile_img, :role)');
          $req->execute(array(
            'pseudo' => $pseudo,
            'email' => $email,
            'salt' => $uuid,
            'password' => $cryptpsw,
            'news' => intval($news),
            'date_inscription' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER["REMOTE_ADDR"],
            'profile_img' => "profiles/no_profile.png",
            'role' => $role
          ));
        }
    }else {
      return -2;
    }
  }else {
    return -1;
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
/*
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
*/