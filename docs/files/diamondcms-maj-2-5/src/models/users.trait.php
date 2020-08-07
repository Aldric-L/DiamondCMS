<?php 
/**
 * Ce trait a pour but de condenser toutes les méthodes qui sont utilisées pour obtenir des informations sur les membres dans le manager et dans la classe User.
 * 
 * @author Aldric L.
 * @copyright 2020
 */
trait Users
{

 /**
  * getInfos - Fonction permettant de récuperer toutes les informations sur un membre stockées dans la table ("d_membre)
  * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
  * @author Aldric.L
  * @copyright Copyright 2020 Aldric L.
  * @access public
  * @param string $pseudo : pseudo du membre
  * @return array
  */
  public function getInfos(&$db, &$pseudo){
    return simplifySQL\select($db, true, "d_membre", array("id", "pseudo", "email", "password", "role", "money", "profile_img", array("date_last_vote", "%d/%m/%Y à %Hh:%imin", "date_last_vote"), "votes", array("date_inscription", "%d/%m/%Y à %Hh:%imin", "date_inscription")), array(array("pseudo", "=", $pseudo)));

  }

 /**
  * getInfosFromId - Fonction permettant de récuperer toutes les informations sur un membre stockées dans la table ("d_membre)
  * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
  * @author Aldric.L
  * @copyright Copyright 2020 Aldric L.
  * @access public
  * @param int $id : id du membre
  * @return array
  */
  public function getInfosFromId(&$db, &$id){
    return simplifySQL\select($db, true, "d_membre", array("id", "pseudo", "email", "password", "role", "money", "profile_img", array("date_last_vote", "%d/%m/%Y à %Hh:%imin", "date_last_vote"), "votes", array("date_inscription", "%d/%m/%Y à %Hh:%imin", "date_inscription")), array(array("id", "=", $id)));
  }

  /**
   * getRoleNameById - Fonction pour récuperer le nom d'un role à partir de son id
   * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
   * @author Aldric.L
   * @copyright Copyright 2020 Aldric L.
   * @access public
   * @return false|array
   */
    public function getRoleNameById($db, $id_role){
      $n = simplifySQL\select($db, true, "d_roles", "name", array(array("id", "=", $id_role)));
      if (!empty($n)){
        return $n['name'];
      }else {
        return false;
      }
        
    }

    /**
     * echoRoleName - Fonction gérant l'affichage des grades devant les pseudos
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 Aldric L.
     * @access public
     * @return string
     */
    public function echoRoleName($db, $pseudo){
        $r = simplifySQL\select($db, true, "d_membre", "role", array(array("pseudo", "=", $pseudo)));
        if (!empty($r)){
          $n = simplifySQL\select($db, true, "d_roles", "name, level", array(array("id", "=", $r['role'])));
          if (!empty($n)){
            if ($n['level'] >= 1){
              return "[" . $n['name'] . "] ";
            }else {
              return "";
            }
          }else {
            return "";
          }
        }else {
          return "";
        }
          
    }

    /**
     * getRoleLevel - Fonction permettant d'obtenir le level du role à partir de son id
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 Aldric L.
     * @access public
     * @param $db : PDO instance
     * @param string $id_role : OBLIGATOIRE si cette methode n'est pas appelée depuis la classe User (cette utilisation est dépreciée depuis la 1.1)
     * @return array
     */
    public function getRoleLevel($db, $id_role=false){
        if ($id_role == false){
            $id_role = $this->role['id'];
        }
        return simplifySQL\select($db, true, "d_roles", "level", array(array("id", "=", $id_role)));
    }

    /**
     * getRoleLevelByPseudo - Fonction pour récuperer le level d'un membre à partir de son pseudo
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 Aldric L.
     * @access public
     * @return false|array
     */
      public function getRoleLevelByPseudo($pseudo){
        $membre = simplifySQL\select($this->bddConnexion(), true, "d_membre", "role", array(array("pseudo", "=", $pseudo)));
        if (!empty($membre)){
          return simplifySQL\select($this->bddConnexion(), true, "d_roles", "level", array(array("id", "=", $membre['role'])))['level'];
        }else {
          return false;
        }
      }

    /**
     * getPseudo - Fonction pour récuperer le pseudo d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 Aldric L.
     * @access public
     * @return false|array
     */
    public function getPseudo($id){
        $membre = simplifySQL\select($this->bddConnexion(), true, "d_membre", "id, pseudo", array(array("id", "=", $id)));
        if (!empty($membre)){
            return $membre['pseudo'];
        }else {
            return false;
        }
    }

    /**
     * getRole - Fonction pour récuperer le nom du role par le pseudo d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 Aldric L.
     * @access public
     * @return false|string
     */
    public function getRole($db, $pseudo){
        $role = simplifySQL\select($db, true, "d_membre", "role", array(array("pseudo", "=", $pseudo)));
        if (!empty($role) && isset($role['role'])){
            return $this->getRoleNameById($db, $role['role']);
        }
    }
}