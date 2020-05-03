<?php
class User {
  private $pseudo;
  private $id;
  private $infos = array();
  private $role = array();

  function __construct($pseudo, $db){
      $this->pseudo = $pseudo;
      $this->infos = $this->getInfos($db);
      $this->id = $this->infos['id'];
      $this->role['id'] = $this->infos['role'];
      $this->getRoleLevel($db);
      $this->getRoleName($db);
  }


  public function reload($db){
    $this->infos = $this->getInfosFromId($db);
    $this->pseudo = $this->infos['pseudo'];
    $this->role['id'] = $this->infos['role'];
    $this->getRoleLevel($db);
    $this->getRoleName($db);
  }

  /**
  * Fonction utilisée par le constructeur pour récupérer les informations
  * @access private
  **/
  private function getInfos($db){
    $req = $db->prepare('SELECT id, pseudo, email, password, role, money, profile_img, DATE_FORMAT(date_last_vote, \'%d/%m/%Y à %Hh:%imin\') AS date_last_vote, votes, DATE_FORMAT(date_inscription, \'%d/%m/%Y à %Hh:%imin\') AS date_inscription FROM d_membre WHERE pseudo = "' . $this->pseudo . '"');

    //On execute la requete
    $req->execute();
    //On récupère tout
    $infos = $req->fetch();
    //On ferme la requete
    $req->closeCursor();

    return $infos;
  }

  /**
  * Fonction utilisée par le constructeur pour récupérer les informations
  * @access private
  **/
  private function getInfosFromId($db){
    //$req = $db->prepare('SELECT id, pseudo, email, password, role, money, profile_img, DATE_FORMAT(date_last_vote, \'%d/%m/%Y à %Hh:%imin\') AS date_last_vote, votes, DATE_FORMAT(date_inscription, \'%d/%m/%Y à %Hh:%imin\') AS date_inscription FROM d_membre WHERE pseudo = "' . $this->pseudo . '"');
    return simplifySQL\select($db, true, "d_membre", array("id", "pseudo", "email", "password", "role", "money", "profile_img", array("date_last_vote", "%d/%m/%Y à %Hh:%imin", "date_last_vote"), "votes", array("date_inscription", "%d/%m/%Y à %Hh:%imin", "date_inscription")), array(array("id", "=", $this->id)));
  }

  /**
  * Fonctions utilisées par n'importe qui pour récupérer les informations précedemment récupérées par le constructeur
  * @access public
  **/
  function getInfo(){
    return $this->infos;
  }

  function isAdmin(){
    if ($this->role['level'] >= 4){
      return true;
    }else {
      return false;
    }
  }

  function getPseudo(){
    return $this->pseudo;
  }

  function getImg(){
    return $this->infos['profile_img'];
  }
  
  function getId(){
    return $this->infos['id'];
  }

  function getMoney(){
    return $this->infos['money'];
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

  function credit($db, $money){
    $curmoney = $this->getMoney();
    $this->infos['money'] = intval($curmoney) + intval($money);
    return simplifySQL\update($db, "d_membre", array(array("money", "=", intval($curmoney) + intval($money))), array(array("id", "=", $this->getId())));
  }

  function var_dump(){
    return var_dump(array($this->role, $this->infos, $this->pseudo));
  }

}
