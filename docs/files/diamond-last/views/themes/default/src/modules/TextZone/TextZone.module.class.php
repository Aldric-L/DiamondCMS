<?php 

namespace ModulesManager;

class TextZone extends Module{
    protected static string $view_path = "TextZone.view.php";

    public static string $name = "TextZone";
    public static string $description = "Juste une zone où inscrire du texte formaté. Attention, nécessite TinyMCE pour fonctionner !";
    public static string $owner = "DiamondCMS";
    public static bool $canBeLoadedTwice = false;
    public static int $allowCache = ModulesManager::CACHE_STATIC;
    public static array $compatiblePages = array();
    public static array $JS = array("TextZone.view.js");

    protected string $fromconfig = "";

    public static function canBeInitializedWithDefault(\PDO $db) : bool { return true; }

    public static function getDefaultConstructorArguments(\PDO $db) : array { return array(); }

    public function __construct(string $init_path, \PDO $db, \ModulesManager\ModulesManager &$mm){
        parent::__construct($init_path, $db, $mm);
        $conf = $mm->getMyConfig(self::$name, "textzone.ftxt");
        if ($conf === null || $conf == ""){
            \ob_start(); ?>
            <p class="text-center"><em>Vous pouvez éditer cette zone de texte en activant le mode edition de la page.<br>N'oubliez pas nos nombreux alias comme {SERVER_NAME} pour le nom du serveur, ou {SERVER_MONEY} !</em></p>
            <?php 
            $conf = \ob_get_clean();
            $mm->setMyConfig(self::$name, "textzone.ftxt", $conf);
        }
        $this->fromconfig = $conf;
    }


    public function render(bool $editing_mode=false) : string {
        $content = $this->fromconfig;
        if (!$editing_mode){            
            foreach (TEXT_ALIAS as $key => $a){
                $content = str_replace($key, $a, $content);
            }
        }

        \ob_start();
        require(self::$view_path);
        $output = \ob_get_contents();
        \ob_end_clean();
        return $output;
    }
}