<?php
/**
 * isAccount - Fonction pour savoir si le couple pseudo-mot de passe est bon
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return boolean
 * @access public
 */
function isAccount($db, $pseudo, $mdp){
  //On hâche le mot de passe pour pouvoir le comparé à ceux stoqués dans la BDD
  $password = sha1($mdp);

  //On récupère tous les membres ayants le pseudo $pseudo et le mot de passe $mdp
  $req = $db->prepare('SELECT id, pseudo FROM d_membre WHERE pseudo = :pseudo AND password = :password');

  //On passe les paramètres
  $req->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
  $req->bindParam(':password', $password, PDO::PARAM_STR);

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
