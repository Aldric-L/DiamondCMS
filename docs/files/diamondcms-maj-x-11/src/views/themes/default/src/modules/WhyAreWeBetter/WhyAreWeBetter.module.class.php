<?php 

namespace ModulesManager;

class WhyAreWeBetter extends Module{
    protected static string $view_path = "WhyAreWeBetter.view.php";

    public static string $name = "WhyAreWeBetter";
    public static string $description = "Présentation de 3 points qui montrent la supériorité de votre réseau sur les autres concurrents.";
    public static string $owner = "DiamondCMS";
    public static bool $canBeLoadedTwice = false;
    //On allow le cache car le JS ne sert qu'en editing, or en editing le cache n'est jamais chargé
    public static int $allowCache = ModulesManager::CACHE_STATIC;
    public static array $compatiblePages = array();
    public static array $JS = array("WhyAreWeBetter.view.js");

    protected array $config = array();

    public static function canBeInitializedWithDefault(\PDO $db) : bool { return true; }

    public static function getDefaultConstructorArguments(\PDO $db) : array { return array(); }

    public function __construct(string $init_path, \PDO $db, \ModulesManager\ModulesManager &$mm){
        parent::__construct($init_path, $db, $mm);
        $conf = $mm->getMyConfig(self::$name, "gen_whyarewebetter.json");
        if ($conf === null || $conf == ""){
            $conf = \file_get_contents($init_path . "default_config.json");
            /*
            Configuration par défaut :
            $conf = json_encode(array(
                "col_1" => array(
                    "icon" => "fa fa-exchange",
                    "title" => "Une Connexion",
                    "desc" => "Grâce à la connexion exceptionnelle dont disposent nos serveurs, plus de latences, à vous les parties sans lags !",
                ),
                "col_2" => array(
                    "icon" => "fa fa-server",
                    "title" => "Des serveurs",
                    "desc" => "Bienvenue sur nos nouveaux serveurs sur-puissants ! Grâce à leurs core i7 et leurs nouvelles puces réseau, à vous les parties endiablées sur d'énormes maps !",
                ),
                "col_3" => array(
                    "icon" => "fa fa-shield",
                    "title" => "Un Systeme de Protection",
                    "desc" => "Nos serveurs disposent des tout derniers systèmes anti-cheat. Ne vous souciez plus des cheaters, ils sont tout bonnement éradiqués !",
                ),
                "animations" : true,
            ), JSON_PRETTY_PRINT);*/
            $mm->setMyConfig(self::$name, "gen_whyarewebetter.json", $conf);
        }
        if ($conf != null)
            $this->config = \json_decode($conf, true);
    }

    public function render(bool $editing_mode=false) : string {
        \ob_start();
        require(self::$view_path);
        $output = \ob_get_contents();
        \ob_end_clean();
        return $output;
    }
}