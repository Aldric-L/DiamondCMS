<?php 

namespace ModulesManager;

class OurTeam extends Module{
    protected static string $view_path = "OurTeam.view.php";

    public static string $name = "OurTeam";
    public static string $description = "Liste des membres de ayant un rôle sur le site.";
    public static string $owner = "DiamondCMS";
    public static bool $canBeLoadedTwice = true;
    public static int $allowCache = ModulesManager::CACHE_SEMISTATIC;
    public static array $compatiblePages = array();

    private $staff = array();


    public static function canBeInitializedWithDefault(\PDO $db) : bool { return true; }

    public static function getDefaultConstructorArguments(\PDO $db) : array { return array(); }

    public function __construct(string $init_path, \PDO $db, \ModulesManager\ModulesManager &$mm){
        parent::__construct($init_path, $db, $mm);
        global $controleur_def;
        $membres = \simplifySQl\select($controleur_def->bddConnexion(), false, "d_membre", "*", false);
        $this->staff = array();
        for($i =0; $i < sizeof($membres); $i++){
            if (intval($controleur_def->getRoleLevel($controleur_def->bddConnexion(), $membres[$i]['role'])) >= 3){
                $membres[$i]['role_name'] = $controleur_def->getRoleNameById($controleur_def->bddConnexion(), $membres[$i]['role']);
                if (!$membres[$i]['is_ban'])
                    array_push($this->staff, $membres[$i]);
            }
        }
 
    }


    public function render(bool $editing_mode=false) : string {
        \ob_start();
        require(self::$view_path);
        $output = \ob_get_contents();
        \ob_end_clean();
        return $output;
    }
}