<?php 

namespace ModulesManager;

class LatestNews extends Module{
    protected static string $view_path = "LatestNews.view.php";

    public static string $name = "LatestNews";
    public static string $description = "Liste des dernières news enregistrées sur le site.";
    public static string $owner = "DiamondCMS";
    public static bool $canBeLoadedTwice = false;
    public static int $allowCache = 0;
    public static array $compatiblePages = array();
    public static array $JS = array("LatestNews.view.js");

    public static function canBeInitializedWithDefault(\PDO $db) : bool { return true; }

    public static function getDefaultConstructorArguments(\PDO $db) : array { return array(); }

    public function render(bool $editing_mode=false) : string {
        \ob_start();
        require(self::$view_path);
        $output = \ob_get_contents();
        \ob_end_clean();
        return $output;
    }
}