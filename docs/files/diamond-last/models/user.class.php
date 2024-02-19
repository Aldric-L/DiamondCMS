<?php
/**
 * Cette classe est utilisée en session pour accèder aux différentes informations du membre connecté.
 * Elle permet aussi de proprement modifier les informations et attributs du membre
 * 
 * Elle remplace avec ses méthodes statiques l'ancien trait Users.
 * 
 * @author Aldric L.
 * @copyright 2020, 2022
 */
class User{
  protected string $pseudo;
  protected int $id;
  protected $infos = array();
  protected array $role = array();
  protected array $rolesInfosCache = array();

  /**
   * Evolution ! $pseudo peut désormais aussi être l'id
   * @param array $infos : si on a déjà récupéré les informations du joueur, notamment en hydrataion.
   */
  function __construct(string $pseudo, PDO $db, $infos=null){
      if (is_numeric($pseudo)){
        $this->id = $pseudo;
        if (!is_null($infos))
          $this->infos = $infos;
        else
          $this->infos = self::getInfosFromId($db, $pseudo);
        if (empty($this->infos) || $this->infos == false)
          throw new Exception("Unable to find user's account", 332);
        $this->pseudo = $this->infos['pseudo'];
      }else {
        $this->pseudo = $pseudo;
        if (!is_null($infos))
          $this->infos = $infos;
        else
          $this->infos = self::getInfosFromPseudo($db, $pseudo);
        if (empty($this->infos) || $this->infos == false)
          throw new Exception("Unable to find user's account", 332);
        $this->id = intval($this->infos['id']);
      }
        
      $this->rolesInfosCache = self::getRolesInfos($db);
      $this->role['id'] = $this->infos['role'];
      if (!isset($this->rolesInfosCache[$this->infos['role']]))
        throw new DiamondException("Unable to find User's role id", "native$337");
      $this->role['level'] = $this->rolesInfosCache[$this->infos['role']]['level'];
      $this->role['name'] = $this->rolesInfosCache[$this->infos['role']]['name'];
  }

  public function reload(PDO $db){
    $this->infos = $this->getInfosFromId($db, $this->id);
    if (array_key_exists("ip", $this->infos) && !empty($this->infos['ip']) && $this->infos['ip'] != $_SERVER['REMOTE_ADDR'])
        throw new DiamondException("Security Alert, IP changed", "native$330");

    $this->pseudo = $this->infos['pseudo'];
    $this->role['id'] = $this->infos['role'];
    $this->rolesInfosCache = self::getRolesInfos($db);
    if (!isset($this->rolesInfosCache[$this->infos['role']]))
      throw new DiamondException("Unable to find User's role id", "native$337");
    $this->role['level'] = $this->rolesInfosCache[$this->infos['role']]['level'];
    $this->role['name'] = $this->rolesInfosCache[$this->infos['role']]['name'];
    if ($this->isBanned())
      throw new DiamondException("A banned user account cannot be reloaded.", "native$332");
  }

  /**
  * Fonctions utilisées par n'importe qui pour récupérer les informations précedemment récupérées par le constructeur
  * @access public
  **/
  public function getInfo(){
    return $this->infos;
  }

  public function getInfos(){
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

  public function getNbConnections(){
    return intval($this->infos['nb_connections']);
  }

  public function getLastConnection(){
    return $this->infos['date_last_connect'];
  }
  
  public function getId(){
    return $this->infos['id'];
  }

  public function getMoney($db=false){
    if ($db != false && $db instanceof \PDO){
      $this->reload($db);
    }
    return intval($this->infos['money']);
  }

  public function getArrayRole(){
    return $this->role;
  }

  public function getRole(){
    return intval($this->role['id']);
  }

  public function getRName(){
    return $this->role['name'];
  }

  public function getRoleName(){
    return $this->role['name'];
  }

  public function getLevel(){
    return intval($this->role['level']);
  }

  public function getEmail(){
    return $this->infos['email'];
  }

  public function getVotes(){
    return intval($this->infos['votes']);
  }

  public function getLastDateVote($formated=true){
    return $formated ? $this->infos['date_last_vote'] : $this->infos['date_last_vote_raw'];
  }

  public function getForumSignature(){
    return htmlspecialchars_decode(DiamondShortcuts\utf8_decode((isset($this->infos['signature']) ? $this->infos['signature'] : "")));
  }

  public function isOkToGetMails(){
    return boolval($this->infos['news']) ? true : false;
  }

  public static function getOneForumSignature(\PDO $db, int $user_id){
    $infos = self::getInfosFromId($db, $user_id);
    if (!is_array($infos) || $infos == false || !array_key_exists("signature", $infos))
      return false;
    return htmlspecialchars_decode(DiamondShortcuts\utf8_decode((isset($infos['signature']) ? $infos['signature'] : "")));
  }

  public function isBanned(){
    return (intval($this->infos['is_ban']) === 1) ? true : false;
  }

  public function getRBan(){
    if (!$this->isBanned())
      return "Erreur ! L'utilisateur n'est pas banni.";
    return ($this->infos['r_ban']  == "") ? "Aucune raison n'a été fournie" : $this->infos['r_ban'];
  }

  public function getProfileImg(){
    return ($this->infos['profile_img']  == null) ? "profiles/no_profile.png" : $this->infos['profile_img'];
  }

  public function setPDP($db, string $pdp="profiles/no_profile.png"){
    $set = simplifySQL\update($db, "d_membre", array(array("profile_img", "=", $pdp)), array(array("id", "=", $this->id)));
    if (!$set)
      return false;

    if ($this->infos['profile_img'] != "profiles/no_profile.png"){
        if (@unlink(ROOT . 'views/uploads/img/' . $this->infos['profile_img']) == false){
            throw new DiamondException ("Unable to delete profile_img file.", "native$540");
        }

        //On purge le cache, car la PDP change et il faut donc propager les changements
        DiamondCache::cacheCleaner(ROOT . "tmp/img/", true);
    }
    $this->reload($db);
    return $set;
  }

  public function credit($db, $money){
    $curmoney = $this->getMoney($db);
    $this->infos['money'] = intval($curmoney) + intval($money);
    return simplifySQL\update($db, "d_membre", array(array("money", "=", intval($curmoney) + intval($money))), array(array("id", "=", $this->getId())));
  }

  public function getRealPayements($db){
    return self::getUserRealPayements($db, $this->id);
  }

  public static function getUserRealPayements(PDO $db, int $id){
    $pp = simplifySQL\select($db, false, "d_boutique_paypal", "*", array(array("user", "=", $id), "AND", array("payment_status", "=", "approved")));
    $dp = simplifySQL\select($db, false, "d_boutique_dedipass", "*", array(array("id_user", "=", $id)));
    return array("PayPal" => $pp, "Dedipass" => $dp);
  }

  public function getCommandes($db){
    return self::getUserCommandes($db, $this->id);
  }

  public static function getUserCommandes(PDO $db, int $id){
    $commandes = simplifySQL\select($db, false, "d_boutique_achats", "*", array(array("id_user", "=", $id)), "id", true);
    foreach ($commandes as $k => $c){
      $commandes[$k]['article'] = simplifySQL\select($db, true, "d_boutique_articles", "*", array(array("id", "=", $commandes[$k]['id_article'])));
    }
    return $commandes;
  }

  public function var_dump(){
    return var_dump(array($this->role, $this->infos, $this->pseudo));
  }


  public function ban(PDO $db, User $bywho, string $r_ban=null){
    if ($this->isBanned())
      return false;
    return self::banUser($db, $this->id, $bywho, $r_ban);
  }

  public static function banUser(PDO $db, int $id, User $bywho, string $r_ban=null){
    $set = simplifySQL\update($db, "d_membre", array(
      array("is_ban", "=", true), 
      array("r_ban", "=", $r_ban), 
      array("date_ban", "=", date("Y-m-d")), 
      array("user_id_ban", "=", $bywho->getId()),
      array("user_role_ban", "=", $bywho->getRole())
    ), array(array("id", "=", $id)));
    if (!$set)
      return false;
    return true;
  }

  public function deban(PDO $db){
    if (!$this->isBanned())
      return false;
    return self::debanUser($db, $this->id);
  }

  public function hasVotedToday() : bool{
    if ($this->getLastDateVote(false) == null OR $this->getLastDateVote(false) == "" or $this->getLastDateVote(false) == "null")
      return false;
    $datetime = strtotime(date("Y-m-d H:i:s"));
    $date2 = strtotime($this->getLastDateVote(false));
    $diff = abs($datetime - $date2);

    return ($diff/86400 <= 1) ? true : false;
  }

  public static function hasOnesVotedToday(PDO $db, int $id) : bool{
    $datetime = strtotime(date("Y-m-d H:i:s"));
    $infos = self::getInfosFromId($db, $id);
    if (empty($infos) || is_bool($infos) || !array_key_exists("date_last_vote_raw", $infos))
      throw new DiamondException("No account found", 335);
    $date2 = strtotime($infos['date_last_vote_raw']);
    $diff = abs($datetime - $date2);
    return ($diff/86400 <= 1) ? true : false;
  }

  public static function debanUser(PDO $db, int $id){
    $set = simplifySQL\update($db, "d_membre", array(array("is_ban", "=", false), array("r_ban", "=", null), array("date_ban", "=", null), array("user_id_ban", "=", null), array("user_role_ban", "=", null)), array(array("id", "=", $id)));
    if (!$set)
      return false;
    return true;
  }

  public function setPseudo(PDO $db, string $pseudo){
    if ($pseudo == $this->pseudo)
      return true;
      
    $r= self::setUserPseudo($db, $this->id, $pseudo);
    if ($r == false)
      return false;
    $this->reload($db);
    return $r;
  }

  public static function setUserPseudo(PDO $db, int $id, string $pseudo){
    // On vérifie que le pseudo n'est pas déja utilisé
    if (self::getInfosFromPseudo($db, $pseudo) != false)
      return false;

    $set = simplifySQL\update($db, "d_membre", array(array("pseudo", "=", $pseudo)), array(array("id", "=", $id)));
    if (!$set)
      return false;
    return true;
  }

  public function setEmail(PDO $db, string $email){
    if ($email == $this->infos['email'])
      return true;
      
    $r= self::setUserEmail($db, $this->id, $email);
    if ($r == false)
      return false;
    $this->reload($db);
    return $r;
  }

  public static function setUserEmail(PDO $db, int $id, string $email){
    $check_email = simplifySQL\select($db, false, "d_membre", "id, email", array(array("email", "=", $email)));
    if (is_array($check_email) && !empty($check_email))
      throw new DiamondException("Unable to update email, because this email is already used by an account.", "native$333");

    $set = simplifySQL\update($db, "d_membre", array(array("email", "=", $email)), array(array("id", "=", $id)));
    if (!$set)
      return false;
    return true;
  }

  public function setRBan(PDO $db, string $r_ban){
    if (!$this->isBanned() || $r_ban == $this->infos['r_ban'])
      return true;
      
    $r= self::setUserRBan($db, $this->id, $r_ban);
    if ($r == false)
      return false;
    $this->reload($db);
    return $r;
  }

  public static function setUserRBan(PDO $db, int $id, string $r_ban){
    $set = simplifySQL\update($db, "d_membre", array(array("r_ban", "=", $r_ban)), array(array("id", "=", $id)));
    if (!$set)
      return false;
    return true;
  }

  public function setMoney(PDO $db, int $money){
    if ($money > 2147483647)
      throw new DiamondException("Invalid value for user's money : max 2 147 483 647 tokens.", "native$301");

    if ($money == $this->infos['money'])
      return true;
      
    $r= self::setUserMoney($db, $this->id, $money);
    if ($r == false)
      return false;
    $this->reload($db);
    return $r;
  }

  public static function setUserMoney(PDO $db, int $id, int $money){
    if ($money > 2147483647)
      throw new DiamondException("Invalid value for user's money : max 2 147 483 647 tokens.", "native$301");

    $set = simplifySQL\update($db, "d_membre", array(array("money", "=", $money)), array(array("id", "=", $id)));
    if (!$set)
      return false;
    return true;
  }

  public function setRole(PDO $db, int $role_id){
    if ($role_id == $this->infos['role'])
      return true;
      
    $r= self::setUserRole($db, $this->id, $role_id);
    if ($r == false)
      return false;
    $this->reload($db);
    return $r;
  }

  public static function setUserRole(PDO $db, int $id, int $role_id){
    // On vérifie que le role existe
    $test = simplifySQL\select($db, true, "d_roles", "*", array(array("id", "=", $role_id)));
    if (empty($test) || $test == false)
      return false;

    $set = simplifySQL\update($db, "d_membre", array(array("role", "=", $role_id)), array(array("id", "=", $id)));
    if (!$set)
      return false;
    return true;
  }


  public static function getSelectFetchInGetInfos() {
    return array("id", "pseudo", "email", "password", "role", "money", "signature", 
    "recovery_code", "recovery_deadline", "ip", "profile_img", "news", "votes",
    array("date_last_vote", "%d/%m/%Y à %Hh%i", "date_last_vote"), 
    array("date_last_vote", "%Y-%m-%d %H:%i:%s", "date_last_vote_raw"), 
    array("date_inscription", "%d/%m/%Y à %Hh:%imin", "date_inscription"), 
    "is_ban", "r_ban", "date_ban", "user_id_ban", "user_role_ban", "nb_connections", 
    array("date_last_connect", "%d/%m/%Y à %Hh:%imin", "date_last_connect"));
  }

  /**
  * getInfos - Fonction permettant de récuperer toutes les informations sur un membre stockées dans la table ("d_membre)
  * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
  * @author Aldric.L
  * @copyright Copyright 2020-2022 Aldric L.
  * @access public
  * @param PDO $db : accès à la BDD
  * @param string $pseudo : pseudo du membre
  * @return array
  */
  public static function getInfosFromPseudo(PDO $db, string $pseudo){
    return simplifySQL\select($db, true, "d_membre", self::getSelectFetchInGetInfos() , array(array("pseudo", "=", $pseudo)));
  }

 /**
  * getInfosFromId - Fonction permettant de récuperer toutes les informations sur un membre stockées dans la table ("d_membre)
  * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
  * @author Aldric.L
  * @copyright Copyright 2020-2022 Aldric L.
  * @access public
  * @param PDO $db : accès à la BDD
  * @param int $id : id du membre
  * @return array
  */
  public static function getInfosFromId(PDO $db, int $id){
    return simplifySQL\select($db, true, "d_membre", self::getSelectFetchInGetInfos(), array(array("id", "=", $id)));
  }

  /**
   * getRoleNameById - Fonction pour récuperer le nom d'un role à partir de son id
   * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
   * @author Aldric.L
   * @copyright Copyright 2020-2022 Aldric L.
   * @access public
   * @param PDO $db : accès à la BDD
   * @param int $id_role : identifiant du rôle à chercher
   * @return false|array
   */
    public static function getRoleNameById(PDO $db, int $id_role){
      $n = simplifySQL\select($db, true, "d_roles", "name", array(array("id", "=", $id_role)));
      if (!empty($n)){
        return $n['name'];
      }else {
        return false;
      }   
    }


  /**
   * getRolesInfos - Fonction pour récuperer les infos sur les roles
   * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
   * @author Aldric.L
   * @copyright Copyright 2023 Aldric L.
   * @access public
   * @param PDO $db : accès à la BDD
   * @return false|array
   */
  public static function getRolesInfos(PDO $db){
    $n = simplifySQL\select($db, false, "d_roles", "*");
    if (!empty($n)){
      $return = array();
      foreach ($n as $k => $role){
        $return[intval($role['id'])] = $role;
      }
      return $return;
    }
    return false;
  }



    /**
     * UserGetRoleNameById - Fonction pour récuperer le nom d'un role à partir de son id
     * En version non-statique elle utilise le cache et évite les appels inutiles en BDD
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @access public
     * @param int $id_role : identifiant du rôle à chercher
     * @return false|array
     */
    public function UserGetRoleName(int $id_role){
      if (isset($this->rolesInfosCache[$id_role]))
        return $this->rolesInfosCache[$id_role]['name'];
      return false;
    }

     /**
     * UserGetRoleLevel - Fonction pour récuperer le level d'un role à partir de son id
     * En version non-statique elle utilise le cache et évite les appels inutiles en BDD
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @access public
     * @param int $id_role : identifiant du rôle à chercher
     * @return false|array
     */
    public function UserGetRoleLevel(int $id_role){
      if (isset($this->rolesInfosCache[$id_role]))
        return $this->rolesInfosCache[$id_role]['level'];
      return false;
    }

    /**
     * UserGetRoleInfos - Fonction pour récuperer les infos d'un role à partir de son id
     * En version non-statique elle utilise le cache et évite les appels inutiles en BDD
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @access public
     * @param int $id_role : identifiant du rôle à chercher
     * @return false|array
     */
    public function UserGetRoleInfos(int $id_role){
      if (isset($this->rolesInfosCache[$id_role]))
        return $this->rolesInfosCache[$id_role];
      return false;
    }

    

    /**
     * echoRoleName - Fonction gérant l'affichage des grades devant les pseudos
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L
     * @param PDO $db : accès à la BDD
     * @param string|int $pseudo : pseudo ou id du membre
     * @access public
     * @return string
     */
    public static function echoRoleName(PDO $db, $pseudo){
        $r = simplifySQL\select($db, true, "d_membre", "role", array(array("pseudo", "=", $pseudo)));
        if (!empty($r) || is_numeric($pseudo)){
          if (is_numeric($pseudo)){
            $r = simplifySQL\select($db, true, "d_membre", "role", array(array("id", "=", intval($pseudo))));
            if (empty($r))
              return "";
          }

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
     * @copyright Copyright 2020-2022 Aldric L.
     * @access public
     * @param PDO $db : PDO instance
     * @param int $id_role : identifiant du rôle
     * @return int
     */
    public static function getRoleLevel(PDO $db, int $id_role) : int{
        $r = simplifySQL\select($db, true, "d_roles", "level", array(array("id", "=", $id_role)));
        if (isset($r['level']))
          return $r['level'];
        return 0;
    }

    /**
     * getRoleLevelByPseudo - Fonction pour récuperer le level d'un membre à partir de son pseudo
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param string $pseudo : pseudo du membre
     * @access public
     * @return int
     */
      public static function getRoleLevelByPseudo(PDO $db, string $pseudo) : int{
        $membre = simplifySQL\select($db, true, "d_membre", "role", array(array("pseudo", "=", $pseudo)));
        if (!empty($membre)){
          $r = simplifySQL\select($db, true, "d_roles", "level", array(array("id", "=", $membre['role'])));
          if (isset($r['level']))
            return $r['level'];
        }
        return 0;
      }

    /**
     * getPseudoById - Fonction pour récuperer le pseudo d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param int $id : id du membre
     * @access public
     * @return false|string
     */
    public static function getPseudoById(PDO $db, int $id){
        $membre = simplifySQL\select($db, true, "d_membre", "id, pseudo", array(array("id", "=", $id)));
        if (!empty($membre)){
            return $membre['pseudo'];
        }else {
            return false;
        }
    }

    /**
     * getOnePseudoById - Fonction pour récuperer le pseudo d'un membre ou "Utilisateur inconnu" et non false comme avec getPseudoById
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param int $id : id du membre
     * @access public
     * @return string
     */
    public static function getOnePseudoById(PDO $db, int $id){
      $membre = simplifySQL\select($db, true, "d_membre", "id, pseudo", array(array("id", "=", $id)));
      if (!empty($membre)){
          return $membre['pseudo'];
      }else {
          return "Utilisateur inconnu";
      }
    }

    /**
     * getRoleByPseudo - Fonction pour récuperer le nom du role par le pseudo d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param string $pseudo : pseudo du membre
     * @access public
     * @return false|string
     */
    public static function getRoleByPseudo(PDO $db, string $pseudo){
        $role = simplifySQL\select($db, true, "d_membre", "role", array(array("pseudo", "=", $pseudo)));
        if (!empty($role) && isset($role['role'])){
            return self::getRoleNameById($db, $role['role']);
        }
        return false;
    }

    /**
     * deleteLastActions - Fonction pour supprimer toutes les interventions du membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @param PDO $db : accès à la BDD
     * @access public
     * @return true|false
     * @throws Exception 335 : erreur interne profonde... sans doute faire un reload avant
     */
    public function deleteLastActions(PDO $db){
      return self::deleteOnesLastActions($db, $this->id);
    }

    /**
     * deleteOnesLastActions - Fonction pour supprimer toutes les interventions d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param id $id : id du membre
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public static function deleteOnesLastActions(PDO $db, int $id){
      $id = simplifySQL\select($db, true, "d_membre", "*", array(array("id", "=", $id)));
      if (!empty($id)){
        if (simplifySQL\delete($db, "d_forum_com", array(array("user", "=", $id['id']))) && 
        simplifySQL\delete($db, "d_forum", array(array("user", "=", $id['id']))))
          return true;
        else 
          return false;
      }else {
        throw new Exception("No account found", 335);
      }
    }

    /**
     * addVote - Fonction pour ajouter un vote au membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param int $bonus : nb de tokens offerts par vote
     * @access public
     * @return true|false
     * @throws Exception 335 : erreur interne profonde... sans doute faire un reload avant
     * @throws Exception 321 : si le compte a déjà voté
     */
    public function addVote(PDO $db, int $bonus=1){
      if ($this->hasVotedToday())
        throw new DiamondException("Vote limit reached.", "native$321");
      return self::addOnesVote($db, $this->id, $bonus);
    }

    /**
     * addVote - Fonction pour ajouter un vote à un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2020-2022 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param int $id : id du membre
     * @param int $bonus : nb de tokens offerts par vote
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     * @throws Exception 321 : si le compte a déjà voté
     */
    public static function addOnesVote(PDO $db, int $id, int $bonus=1){
      $user = simplifySQL\select($db, true, "d_membre", "*", array(array("id", "=", $id)));
      if (!empty($user)){
        if (self::hasOnesVotedToday($db, $id))
          throw new DiamondException("Vote limit reached.", "native$321");

        if (simplifySQL\update($db, "d_membre", array(
            "votes" => (isset($user['votes']) && is_numeric($user['votes'])) ? intval($user['votes'])+1 : 1,
            "money" => (isset($user['money']) && is_numeric($user['money'])) ? intval($user['money'])+$bonus : $bonus,
            "date_last_vote" => date("Y-m-d H:i:s")
          ), array(array("id", "=", $user['id']))))
          return true;
        else
          return false;

      }else {
        throw new Exception("No account found", 335);
      }
    }

    /**
     * setSignature - Fonction pour définir la signature sur le forum du membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param string $signature : signature non-encodée
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public function setSignature(PDO $db, string $signature){
      return self::setOnesSignature($db, $this->id, $signature);
    }

    /**
     * setOnesSignature - Fonction pour définir la signature sur le forum d'un membre
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param id $id : id du membre
     * @param string $signature : signature non-encodée
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public static function setOnesSignature(PDO $db, int $id, string $signature){
      $user = simplifySQL\select($db, true, "d_membre", "*", array(array("id", "=", $id)));
      if (!empty($user)){
        if (simplifySQL\update($db, "d_membre", array(
            "signature" => DiamondShortcuts\utf8_encode(htmlspecialchars($signature))
          ), array(array("id", "=", $user['id']))))
          return true;
        else
          return false;

      }else {
        throw new Exception("No account found", 335);
      }
    }

    /**
     * startPasswordReinitialization - Fonction pour créer le code de réinitialisation de mot de passe
     * Si une réinitialisation est déjà en cours, elle sera relancée quand même.
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param bool $force : faut il recréer un code même si un code est déjà valide
     * @access public
     * @return string|false : string est le code pour réinitialiser le mdp
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public function startPasswordReinitialization(PDO $db, bool $force=false){
      return self::startOnesPasswordReinitialization($db, $this->id, $force);
    }

    /**
     * startOnesPasswordReinitialization - Fonction pour créer le code de réinitialisation de mot de passe
     * Si une réinitialisation est déjà en cours, elle sera relancée quand même.
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param id $id : id du membre
     * @param bool $force : faut il recréer un code même si un code est déjà valide
     * @access public
     * @return string|false : string est le code pour réinitialiser le mdp
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public static function startOnesPasswordReinitialization(PDO $db, int $id, bool $force=false){
      $user = simplifySQL\select($db, true, "d_membre", "*", array(array("id", "=", $id)));
      if (!empty($user)){
        if (!$force && !empty($user) && is_array($user) && array_key_exists("recovery_deadline", $user) && !empty($user["recovery_deadline"]) && !is_null($user["recovery_deadline"]) && (new DateTime("now") < new DateTime($user["recovery_deadline"])))
          throw new DiamondException("Password already reinitializing.", "native$331g");

        if (simplifySQL\update($db, "d_membre", array(
            "recovery_code" => $uuid=uniqid(),
            "recovery_deadline" => date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s'). ' + 3 days'))
          ), array(array("id", "=", $user['id']))))
          return $uuid;
        else
          return false;

      }else {
        throw new DiamondException("No account found", 335);
      }
    }

    /**
     * changePassword - Fonction pour changer le mot de passe utilisateur
     * Cette fonction acceptera de modifier le compte à l'unique condition qu'une procédure de réinitialisation ait bien été lancée préalablement
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param string $password : nouveau mot de passe non-encodé
     * @param bool $can_force : permet de forcer la sécurité de la procédure de réinitialisation de mot de passe
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte ou qu'aucune réinitialisation n'a été dument lancée
     */
    public function changePassword(PDO $db, string $password, bool $can_force=false){
      return self::changeOnesPassword($db, $this->id, $password, $can_force);
    }

    /**
     * startOnesPasswordReinitialization - Fonction pour changer le mot de passe utilisateur
     * Cette fonction acceptera de modifier le compte à l'unique condition qu'une procédure de réinitialisation ait bien été lancée préalablement
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param id $id : id du membre
     * @param string $password : nouveau mot de passe non-encodé
     * @param bool $can_force : permet de forcer la sécurité de la procédure de réinitialisation de mot de passe
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte ou qu'aucune réinitialisation n'a été dument lancée
     */
    public static function changeOnesPassword(PDO $db, int $id, string $password, bool $can_force=false){
      if (strlen($password) < 6)
        throw new DiamondException("Votre mot de passe est trop court et n'est pas assez solide.", 701);

      $user = simplifySQL\select($db, true, "d_membre", "*", array(array("id", "=", $id)));
      if (!empty($user) && is_array($user) && ($can_force OR (array_key_exists("recovery_deadline", $user) 
      && !empty($user["recovery_deadline"]) && !is_null($user["recovery_deadline"]) && (new DateTime("now") < new DateTime($user["recovery_deadline"]))))){
        if (simplifySQL\update($db, "d_membre", array(
            "password" => sha1((!empty($user["salt"]) ? (string)$user["salt"] : "")  . (string)$password),
            "recovery_code" => NULL,
            "recovery_deadline" => NULL,
          ), array(array("id", "=", $user['id']))))
          return true;
        else
          return false;

      }else {
        throw new DiamondException("No account found, or no password reinitialisation started", 335);
      }
    }

    /**
     * emailUnsubscribe - Fonction pour désactiver la réception de mails pour l'utilisateur
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public function emailUnsubscribe(PDO $db){
      return self::setOnesMailPreferences($db, $this->id, false);
    }

    /**
     * setMailPreferences - Fonction pour désactiver la réception de mails pour l'utilisateur
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param bool $news : autoriser ou nom les mails
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public function setMailPreferences(PDO $db, bool $news){
      return self::setOnesMailPreferences($db, $this->id, $news);
    }

    /**
     * setOnesMailPreferences - Fonction pour désactiver la réception de mails pour un utilisateur
     * Cette méthode correspond aux nouvelles normes d'utilisation SQL (2020) en utilisant les fonctions de simplification/sécurisation
     * @author Aldric.L
     * @copyright Copyright 2023 Aldric L.
     * @param PDO $db : accès à la BDD
     * @param id $id : id du membre
     * @param bool $news : autoriser ou nom les mails
     * @access public
     * @return true|false
     * @throws Exception 335 : si l'id ne correspond pas à un compte
     */
    public static function setOnesMailPreferences(PDO $db, int $id, bool $news){
      $user = simplifySQL\select($db, true, "d_membre", "*", array(array("id", "=", $id)));
      if (!empty($user)){
        if (simplifySQL\update($db, "d_membre", array(
            "news" => $news,
          ), array(array("id", "=", $user['id']))))
          return true;
        else
          return false;

      }else {
        throw new DiamondException("No account found", 335);
      }
    }


    /**
     * disconnect - Fonction pour se deconnecter
     * @author Aldric.L
     * @copyright Copyright 2016-2017 Aldric L. 2020, 2022
     * @param User $user : $_SESSION['user']
     * @return void
     * @access public
     */
    public static function disconnect(User $user){
      if ($_SESSION['user'] !== $user)
        throw new DiamondException("Disconnection failure", "native$328");
        
      //On detruit la session
      $_SESSION = array();
      if (isset($_COOKIE['pseudo'])){
        setcookie('pseudo', "", time(), WEBROOT, $_SERVER['HTTP_HOST'], false, true);
        //setcookie("pseudo", "", time() - 3600);
        //unset($_COOKIE['pseudo']);
      }
    }

}
