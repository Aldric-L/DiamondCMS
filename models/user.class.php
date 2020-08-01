<?php
/**
 * Cette classe est utilisée en session pour accèder aux différentes informations du membre connecté.
 * La majorité de ses méthodes sont des getters d'informations.
 * Pour ses appels à la BDD, elle utilise le trait Users.
 * 
 * @author Aldric L.
 * @copyright 2020
 */
class User{
  use Users;
  protected $pseudo;
  protected $id;
  protected $infos = array();
  protected $role = array();

  function __construct($pseudo, $db){
      $this->pseudo = $pseudo;
      $this->infos = $this->getInfos($db, $pseudo);
      $this->id = $this->infos['id'];
      $this->role['id'] = $this->infos['role'];
      $this->role['level'] = $this->getRoleLevel($db, $this->infos['role'])['level'];
      $this->role['name'] = $this->getRoleNameById($db, $this->role['id']);
  }

  public function reload($db){
    $this->infos = $this->getInfosFromId($db, $this->id);
    $this->pseudo = $this->infos['pseudo'];
    $this->role['id'] = $this->infos['role'];
    $this->role['level'] = $this->getRoleLevel($db, $this->infos['role'])['level'];
    $this->role['name'] = $this->getRoleNameById($db, $this->role['id']);
  }

  /**
  * Fonctions utilisées par n'importe qui pour récupérer les informations précedemment récupérées par le constructeur
  * @access public
  **/
  public function getInfo(){
    return $this->infos;
  }

  public function isAdmin(){
    if ($this->role['level'] >= 4){
      return true;
    }else {
      return false;
    }
  }

  public function getPseudo(){
    return $this->pseudo;
  }

  public function getImg(){
    return $this->infos['profile_img'];
  }
  
  public function getId(){
    return $this->infos['id'];
  }

  public function getMoney($db=false){
    if ($db != false){
      $this->reload($db);
    }
    return $this->infos['money'];
  }

  public function getArrayRole(){
    return $this->role;
  }

  public function getRole(){
    return $this->role['id'];
  }

  public function getRName(){
    return $this->role['name'];
  }

  public function getLevel(){
    return $this->role['level'];
  }

  public function credit($db, $money){
    $curmoney = $this->getMoney($db);
    $this->infos['money'] = intval($curmoney) + intval($money);
    return simplifySQL\update($db, "d_membre", array(array("money", "=", intval($curmoney) + intval($money))), array(array("id", "=", $this->getId())));
  }

  public function getRealPayements($db){
    $pp = simplifySQL\select($db, false, "d_boutique_paypal", "*", array(array("user", "=", $this->getId())));
    $dp = simplifySQL\select($db, false, "d_boutique_dedipass", "*", array(array("id_user", "=", $this->getId())));
    return array("PayPal" => $pp, "Dedipass" => $dp);
  }

  public function var_dump(){
    return var_dump(array($this->role, $this->infos, $this->pseudo));
  }

}
