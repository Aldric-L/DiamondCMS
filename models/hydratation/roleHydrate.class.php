<?php 

require_once(ROOT . "models/user.class.php");
class RoleHydrate {

    protected array $infos;
    protected $nb_user=false;
    protected $linked_accounts=false;

    /**
     * 3 initiations possibles : soit par id soit par nom de role (par id role) soit par array (si on a déja récupéré les infos)
     * 
     */
    public function __construct(PDO $db, $id_role, $array=false){
        if ($id_role !== false){
            if (is_string($id_role) && !is_numeric($id_role))
                $i = simplifySQL\select($db, true, "d_roles", "*", array(array("name", "=", $id_role)));
            else
                $i = simplifySQL\select($db, true, "d_roles", "*", array(array("id", "=", $id_role)));
            if (empty($i) || $i == false)
                throw new DiamondException("Unable to find the role. (issue with " . $id_role . ")", 337);
            $this->infos = $i;
        }else if ($array !== false) {
            $this->infos = $array;
        }else {
            throw new Exception("Bad initialisation, please give a way to get role's infos...");
        }
    }

    public function getName(){
        return (isset($this->infos['name']) && is_string($this->infos['name'])) ? $this->infos['name'] : "Erreur";
    }

    public function getLevel(){
        return (isset($this->infos['level']) && is_numeric($this->infos['level'])) ? intval($this->infos['level']) : -1;
    }

    public function getId(){
        return (isset($this->infos['id']) && is_numeric($this->infos['id'])) ? intval($this->infos['id']) : -1;
    }

    public function isDefault(){
        return (isset($this->infos['dflt']) && ($this->infos['dflt'] == true || $this->infos['dflt'] == 1 || $this->infos['dflt'] == '1') ) ? true : false;
    }

    public function getLinkedAccounts(PDO $db){
        if ($this->linked_accounts !== false)
            return $this->linked_accounts;
        
        return $this->linked_accounts = simplifySQL\select($db, false, "d_membre", User::getSelectFetchInGetInfos(), array(array("role", "=", $this->infos['id'])));
    }

    public function getNbUsers(PDO $db){
        if ($this->nb_user !== false)
            return $this->nb_user;

        return is_array($this->getLinkedAccounts($db)) ? $this->nb_user = sizeof($this->getLinkedAccounts($db)) : 0;
    }

    public function canBeDeleted($db){
        return (sizeof($this->getLinkedAccounts($db)) == 0 && $this->infos['dflt'] != true && $this->infos['id'] != 6 && $this->infos['name'] != "diamond_master");
    }

    public function canBeDefault(){
        return ($this->infos['dflt'] != true && $this->infos['id'] != 6 && $this->infos['name'] != "diamond_master");
    }

    public function canBeEdited(){
        return (isset($_SESSION['user']) && intval($_SESSION['user']->getLevel()) > intval($this->infos['level']) && $this->infos['id'] != 6 && $this->infos['name'] != "diamond_master");
    }

    public function setName(PDO $db, $name){
        $set = simplifySQL\update($db, "d_roles", array(
            array("name", "=", $name)
          ), array(array("id", "=", $this->infos['id'])));
        if (!$set)
            return false;
        return true;
    }

    public function setLevel(PDO $db, $level){
        $set = simplifySQL\update($db, "d_roles", array(
            array("level", "=", $level)
          ), array(array("id", "=", $this->infos['id'])));
        if (!$set)
            return false;
        return true;
    }

    public function setDefault(PDO $db){
        if (!$this->canBeDefault())
            return false;
        
        $set = simplifySQL\update($db, "d_roles", array(
            array("dflt", "=", false)
          ), array(array("dflt", "=", true)));

        if (!$set)
            return false;

        $set = simplifySQL\update($db, "d_roles", array(
                array("dflt", "=", true)
        ), array(array("id", "=", $this->infos['id'])));

        if (!$set)
            return false;
            
        return true;
    }

}