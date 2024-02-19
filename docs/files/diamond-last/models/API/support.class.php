<?php 

class support extends DiamondAPI {
    public function __construct($paths, $pdo, $controleur, $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "get_createTicket" => array("titre_ticket", "contenu_ticket"),
            "get_createAnswer" => array("contenu_reponse", "id_ticket"),
            "set_deleteTicket" => array("id"),
            "set_deleteAnswer" => array("id"),
            "set_closeTicket" => array("id"),
            "set_ticketStatus" => array("id", "status"),
        );
        $this->registerAntiSpam(array(
            "get_createTicket" => array(3, 5, 100),
            "get_createAnswer" => array(3, 10, 100),
        ));
    }

    public function get_createTicket(){
        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $this->cleanArg($this->args['contenu_ticket']);

        try{
            if (!simplifySQL\insert($this->getPDO(), "d_support_tickets",
                array("contenu_ticket", "titre_ticket", "pseudo", "date_ticket", "role"),
                array(DiamondShortcuts\utf8_encode(htmlspecialchars($this->args['contenu_ticket'])), htmlspecialchars($this->args['titre_ticket']), 
                $_SESSION['user']->getId(), date('Y-m-d H:i:s'), $_SESSION['user']->getRole()))){
                    throw new DiamondException("Unable to create ticket", "342c");                 
            }  
        }catch (Exception $e){
            throw new DiamondException("Unable to create ticket", "342c");                 
        }
        return $this->formatedReturn(1);
    }

    public function get_createAnswer(){
        if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User))
            throw new DiamondException("A user need to be connected", 701);

        $this->cleanArg($this->args['contenu_reponse']);
        try{
            if (!simplifySQL\insert($this->getPDO(), "d_support_rep",
                array("contenu_reponse", "id_ticket", "pseudo", "date_reponse", "role"),
                array(DiamondShortcuts\utf8_encode(htmlspecialchars($this->args['contenu_reponse'])), $this->args['id_ticket'], 
                $_SESSION['user']->getId(), date('Y-m-d H:i:s'), $_SESSION['user']->getRole()))){
                    throw new DiamondException("Unable to create answer", "342c");                 
            }  
        }catch (Exception $e){
            throw new DiamondException("Unable to create answer (SQL " . $e->getMessage() . ")", "342c");                 
        }

        try{
            if (!simplifySQL\update($this->getPDO(), "d_support_tickets", 
                array(
                    array("status", "=", 1)
                ), array(array("id", "=", $this->args['id_ticket'])))){
                    throw new DiamondException("Error while updating ticket status", "342a");
            }
        }catch (Exception $e){
            throw new DiamondException("Error while updating ticket status", "342a");
        }
        return $this->formatedReturn(1);

    }


    public function set_deleteTicket(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);
        
        try{
            if (simplifySQL\delete($this->getPDO(), "d_support_ticket", array(array("id", "=", $this->args['id']))) != true)
                throw new DiamondException("Error while deleting the ticket", "341b");
        }catch (Exception $e){
            throw new DiamondException("Error while deleting the ticket (2)", "341b");
        }

        try{
            if (simplifySQL\delete($this->getPDO(), "d_support_rep", array(array("id_ticket", "=", $this->args['id']))) != true)
                throw new DiamondException("Error while deleting answers", "341b");
        }catch (Exception $e){
            throw new DiamondException("Error while deleting answers (2)", "341b");
        }
        return $this->formatedReturn(1);
    }

    public function set_deleteAnswer(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);

        try{
            //Si la tâche est associée à aucune commande, on peut la supprimer purement et simplement.
            if (simplifySQL\delete($this->getPDO(), "d_support_rep", array(array("id", "=", $this->args['id']))) != true)
                throw new DiamondException("Error while deleting the answer", "341b");
        }catch (Exception $e){
            throw new DiamondException("Error while deleting the answer (2)", "341b");
        }        
        return $this->formatedReturn(1);
    }

    public function set_closeTicket(){
        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);
        
        $ticket = simplifySQL\select($this->getPDO(), true, "d_support_tickets", 
            array("id, status, contenu_ticket, titre_ticket, pseudo", array("date_ticket", "%d/%m/%Y", "date_t")), 
            array(array("id", "=", $this->args["id"])), "id", true);

        if (is_array($ticket) && array_key_exists("status", $ticket) && intval($ticket['status']) == 2)
            return $this->formatedReturn(1);

        if ($this->level < 2 && (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User) || !is_array($ticket) || !array_key_exists("pseudo", $ticket) || ($_SESSION['user']->getId() != intval($ticket['pseudo']))))
            throw new Exception("Forbidden access", 706);

        $this->setStatus($this->args["id"], 2); 
        return $this->formatedReturn(1);
    }

    public function set_ticketStatus(){
        $this->args = cleanIniTypes($this->args);
        if (!is_numeric($this->args['id']))
            throw new DiamondException("An int is an int (id)", 701);

        if (!is_numeric($this->args['status']) || $this->args['status'] < 0 || $this->args['status'] > 2)
            throw new DiamondException("Status should be an integer between 0 and 2", 701);
        
        $ticket = simplifySQL\select($this->getPDO(), true, "d_support_tickets", 
            array("id, status, contenu_ticket, titre_ticket, pseudo", array("date_ticket", "%d/%m/%Y", "date_t")), 
            array(array("id", "=", $this->args["id"])), "id", true);

        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);

        $this->setStatus($this->args["id"], $this->args["status"]); 
        return $this->formatedReturn(1);
    }

    private function setStatus(int $id, int $status) : void{
        try{
            if (simplifySQL\update($this->getPDO(), "d_support_tickets", array(
                array("status", "=", $status)
            ), array(array("id", "=", $id))) != true)
                throw new DiamondException("Error while updating ticket status", "342a");
        }catch (Exception $e){
            throw new DiamondException("Error while updating ticket status (2)", "342a");
        }
    }
}