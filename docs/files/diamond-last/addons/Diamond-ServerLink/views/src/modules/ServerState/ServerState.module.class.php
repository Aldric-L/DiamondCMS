<?php 

namespace ModulesManager;

class ServerState extends Module{
    protected static string $view_path = "ServerState.view.php";

    public static string $name = "ServerState";
    public static string $description = "Etat des serveurs de jeu configurés dans Diamond-ServerLink.";
    public static string $owner = "Diamond-ServerLink";
    public static array $compatiblePages = array();
    public static bool $canBeLoadedTwice = false;
    public static int $allowCache = 0;
    public static array $JS = array("ServerState.view.js");


    public static function canBeInitializedWithDefault(\PDO $db) : bool { return true; }

    public static function getDefaultConstructorArguments(\PDO $db) : array {
        return array();
    }

    public function render(bool $editing_mode=false) : string {
        \ob_start();
        require(self::$view_path);
        $output = \ob_get_contents();
        \ob_end_clean();
        return $output;
    }
}