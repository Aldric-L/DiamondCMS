<?php

require_once(ROOT . "models/user.class.php");

class UserHydrate extends User {
    private $cur_user;
    private array $lastactions;

    /**
     * 
     * @param array $infos permet d'éviter un appel à la BDD inutile si on a déjà les infos du joueur
     */
    function __construct(string $pseudo, PDO $db, $cur_user=null, $infos=null){
        parent::__construct($pseudo, $db, $infos);
        $this->cur_user = $cur_user;
        $this->lastactions = array();
    }

    public function can_ban(){
        return ($this->cur_user instanceof User && $this->cur_user->getLevel()>= 4 && !$this->isBanned() && $this->getLevel() < $this->cur_user->getLevel() && $this->getId() != $this->cur_user->getId()) ? true : false;
    }

    public function can_deban(){
        return ($this->cur_user instanceof User && $this->cur_user->getLevel()>= 4 && $this->isBanned() && $this->getLevel() < $this->cur_user->getLevel() && $this->getId() != $this->cur_user->getId()) ? true : false;
    }

    public function can_edit(){
        return ($this->cur_user instanceof User && (($this->cur_user->getLevel()>= 4 && $this->getLevel() < $this->cur_user->getLevel()) || ($this->getId() === $this->cur_user->getId()))) ? true : false;
    }

    public function get_cur_user(){
        return $this->cur_user;
    }

    public function isUnderPasswordRecovery(){
        return array_key_exists("recovery_deadline", $this->getInfos()) && !empty($this->getInfos()["recovery_deadline"]) && !is_null($this->getInfos()["recovery_deadline"]) && (new DateTime("now") < new DateTime($this->getInfos()["recovery_deadline"]));
    }

    public function get_underRoles($db){
        if ($this->cur_user === null) return array();
        $rolescanbeselected = array();
        $roles = simplifySQL\select($db, false, "d_roles", "*");
        foreach ($roles as $r){
            if ($r['level'] <= $this->cur_user->getLevel()){
                array_push($rolescanbeselected, $r);
            }
        }
        return $rolescanbeselected;
    }

    public function get_lastActions($db){
        if (empty($this->lastactions)){
            $this->lastactions = simplifySQL\select($db, false, "d_forum_com", 
                                                    array("id", "content_com", "user", "id_post", array("date_comment", "%d/%m/%Y\ à %Hh:%imin", "date_com")), 
                                                    array(array("user", "=", $this->getId())), "date_comment", true, array(0, 10));  
            foreach ($this->lastactions as $key => $lastaction) {
              $this->lastactions[$key]['id_post'] = simplifySQL\select($db, true, "d_forum", "*", array(array('id', '=', $this->lastactions[$key]['id_post'])));              
              if ($this->lastactions[$key]['id_post'] == false || (is_array($this->lastactions[$key]['id_post']) && empty($this->lastactions[$key]['id_post']))){
                  unset($this->lastactions[$key]);
              }else {
                $this->lastactions[$key]['content_com'] = htmlspecialchars_decode(DiamondShortcuts\utf8_decode($this->lastactions[$key]['content_com']));      
                try {
                    $membre = new User($this->lastactions[$key]['id_post']['user'], $db);
                    $this->lastactions[$key]['id_post']['user'] = $membre->getPseudo();
                  }catch (Exception $e){
                    $this->lastactions[$key]['id_post']['user'] = "Utilisateur inconnu";
                  }
              }
            }
        }
        return $this->lastactions;
    }

}