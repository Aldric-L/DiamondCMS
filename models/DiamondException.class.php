<?php
class DiamondException extends Exception{
    private $true_code;
    private $errors_manager;
    private $vanilla_message;

    private $owner = "native";

    public function __construct($message=null, $code = 121, Throwable $previous = null) {
        global $controleur_def;
        if (isset($controleur_def) && $controleur_def instanceof Controleur)
            $this->errors_manager = $controleur_def->getErrorhandler();
        else
            $this->errors_manager = new Errors(ROOT . "config/", ROOT . "logs/");
            
        $this->true_code = $code;
        if (is_numeric($this->true_code) || (is_array(explode("$", $this->true_code)) && sizeof(explode("$", $this->true_code)) === 1)){
            $this->true_code = "native$" . (string)$this->true_code;
        }else if (is_array($carrray = explode("$", $code)) && sizeof($carrray) === 2){
            $this->owner = $carrray[0];
            $code = $carrray[1];
        }
        
        $this->vanilla_message = $message;
        if (!is_numeric($code)){
            if (!is_numeric($code)){
                while (!is_numeric($code) && $code != "")
                    $code = substr($code, 0, -1);
            }
            if ($code == "")
                $code = 121;
        }
        parent::__construct($this->errors_manager->getContentError($this->getTrueCode()), $code, $previous);
    }

    public function getTrueCode(){
        return $this->true_code;
    }                

    public function getVanillaMessage(){
        return $this->vanilla_message;
    }

    // chaîne personnalisée représentant l'objet
    public function __toString() {
        return "[{$this->true_code}]: {$this->message}\n";
    }

    public function getOwner(){
        return $this->owner;
    }
}
