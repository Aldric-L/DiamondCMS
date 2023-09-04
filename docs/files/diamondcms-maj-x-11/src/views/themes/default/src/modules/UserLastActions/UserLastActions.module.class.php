<?php 

namespace ModulesManager;

class UserLastActions extends Module{
    protected static string $view_path = "UserLastActions.view.php";

    public static string $name = "UserLastActions";
    public static string $description = "Liste des dernières actions d'un utilisateur (commentaires et sujets sur le forum).";
    public static string $owner = "DiamondCMS";
    public static bool $canBeLoadedTwice = true;
    public static int $allowCache = 0;
    public static array $compatiblePages = array("compte");

    private \UserHydrate $constructedUser;

    public static function canBeInitializedWithDefault(\PDO $db) : bool {
        $param = $GLOBALS['param'];
        return (((!empty($param[1])) && is_string($param[1]) && !empty($isacc = \User::getInfosFromPseudo($db, $param[1])) && !is_bool($isacc)) || (isset($_SESSION['user']) && $_SESSION['user'] instanceof \User)) ? true : false;
    }

    public static function getDefaultConstructorArguments(\PDO $db) : array {
        $param = $GLOBALS['param'];
        if (!self::canBeInitializedWithDefault($db))
            throw new \DiamondException("Module can't be initilized with default arguments in this configuration", "native$801");
        
        if (!class_exists("\UserHydrate"))
            require_once(ROOT . "models/hydratation/userHydrate.class.php");
        
        return array(
            new \UserHydrate(
                ((!empty($param[1])) && is_string($param[1]) && !empty($isacc = \User::getInfosFromPseudo($db, $param[1])) && !is_bool($isacc)) ? $param[1] : $_SESSION['user']->getPseudo(), 
                $db, null)
        );
    }

    public function __construct(string $init_path, \PDO $db, \ModulesManager\ModulesManager &$mm, \UserHydrate $constructedUser){
        parent::__construct($init_path, $db, $mm);
        $this->constructedUser = $constructedUser;
    }


    public function render(bool $editing_mode=false) : string {
        $user = $this->constructedUser;
        $lastactions = $user->get_lastActions($this->getPDO());
        \ob_start();
        require(self::$view_path);
        $output = \ob_get_contents();
        \ob_end_clean();
        return $output;
    }
}