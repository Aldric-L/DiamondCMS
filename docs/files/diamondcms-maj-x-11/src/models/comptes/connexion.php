<?php
/**
 * isAccount - Fonction pour savoir si le couple pseudo-mot de passe est bon
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L. 2020
 * @return boolean
 * @access public
 * @deprecated Utiliser désormais l'API pour les connexions
 */
function isAccount($db, $Serveur_config, $pseudo, $mdp, $salt){
  //On hâche le mot de passe pour pouvoir le comparé à ceux stoqués dans la BDD
  if (empty($salt)){
    $m = $mdp;
  }else {
    $m = $salt . $mdp;
  }
  $password = sha1(htmlspecialchars($m));

  //On protege le pseudo
  $pseudo_co = htmlspecialchars($pseudo);

  $rep = simplifySQL\select($db, true, "d_membre", "*", array(array("pseudo", "=", $pseudo_co), "AND", array("password", "=", $password)));
  
  //si on trouve un membre, on retourne true
  if ($rep != null){
    if ($Serveur_config['ban_ip']){
      //On récupère tous les membres ayant la même ip pour verifier si elle ne correspond à aucun autre compte banni
      $req2 = $db->prepare('SELECT ip, pseudo FROM d_membre WHERE ip = "' . $_SERVER['REMOTE_ADDR'] . '"');
      $req2->execute();
      //On récupère tout
      $rep2 = $req2->fetch();
      //On ferme la requete
      $req2->closeCursor();
      if (isBan($db, $rep2['pseudo']) != false){
        $db->exec("UPDATE d_membre SET is_ban = 1, r_ban= \"Votre compte a eu la même ip qu'un autre compte banni. Votre compte est donc suspendu. Banni par Console.\" WHERE pseudo = \"$pseudo_co\"");   
        //On ne return rien car le controleur verifira lui même si le compte est banni et l'indiquera à l'utilisateur
        //return false;
      }
    }

    if ($rep['ip'] != $_SERVER['REMOTE_ADDR']){
      $db->exec("UPDATE d_membre SET ip =\"{$_SERVER['REMOTE_ADDR']}\" WHERE id = \"{$rep['id']}\"");
    }
    return true;
  }
  return false;
}

/**
 * isBan - Savoir si le compte est banni
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @deprecated il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * @return boolean
 * @access public
 */
function isBan($db, $pseudo){
  //On protege le pseudo
  $pseudo_co = htmlspecialchars($pseudo);
  //On récupère tous les membres ayants le pseudo $pseudo et le mot de passe $mdp
  $req = $db->prepare('SELECT is_ban, r_ban FROM d_membre WHERE pseudo = :pseudo');
  //On passe les paramètres
  $req->bindParam(':pseudo', $pseudo_co, PDO::PARAM_STR);

  //On execute la requete
  $req->execute();
  //On récupère tout
  $rep = $req->fetch();
  //On ferme la requete
  $req->closeCursor();
  if ($rep['is_ban']){
    if ($rep['r_ban'] == null){
      return "Raison du bannissement non-définie par l'administrateur";
    }
    return $rep['r_ban'];
  }else {
    return false;
  }
}
