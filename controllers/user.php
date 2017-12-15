<?php
class User {
  private $pseudo;
  private $infos = array();
  private $role = array();

  function __construct($pseudo, $db){
      $this->pseudo = $pseudo;
      $this->infos = $this->getInfos($db);
      $this->role['id'] = $this->infos['role'];
      $this->getRoleLevel($db);
      $this->getRoleName($db);
  }

  /**
  * Fonction utilisée par le constructeur pour récuperé les informations
  * @access private
  **/
  function getInfos($db){
    $req = $db->prepare('SELECT id, pseudo, email, password, role, money, DATE_FORMAT(date_last_vote, \'%d/%m/%Y à %Hh:%imin\') AS date_last_vote, votes, DATE_FORMAT(date_inscription, \'%d/%m/%Y à %Hh:%imin\') AS date_inscription, admin FROM d_membre WHERE pseudo = "' . $this->pseudo . '"');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $infos = $req->fetch();
    //On ferme la requete
    $req->closeCursor();

    return $infos;
  }

  /**
  * Fonctions utilisées par n'importe qui pour récuperé les informations précedemment récupérée par le constructeur
  * @access public
  **/
  function getInfo(){
    return $this->infos;
  }

  function isAdmin(){
    return $this->infos['admin'];
  }

  function getPseudo(){
    return $this->pseudo;
  }

  function getArrayRole(){
    return $this->role;
  }

  function getRole(){
    return $this->role['id'];
  }

  function getRName(){
    return $this->role['name'];
  }

  function getLevel(){
    return $this->role['level'];
  }

  function getRoleLevel($db){
    $req = $db->prepare('SELECT level FROM d_roles WHERE id = "' . $this->role['id'] . '"');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $role = $req->fetch();
    //On ferme la requete
    $req->closeCursor();

    return $this->role['level'] = $role['level'];
  }

  function getRoleName($db){
    $req = $db->prepare('SELECT name FROM d_roles WHERE id = "' . $this->role['id'] . '"');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $role = $req->fetch();
    //On ferme la requete
    $req->closeCursor();

    return $this->role['name'] = $role['name'];
  }

  function var_dump(){
    return var_dump(array($this->role, $this->infos, $this->pseudo));
  }

}
