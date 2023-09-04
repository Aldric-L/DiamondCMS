<?php 

namespace ModulesManager;


abstract class Module {
    protected static string $view_path;
    
    public static string $name;
    public static string $description;
    public static string $owner;
    public static bool $canBeLoadedTwice;
    // Pas de Cache si JS !
    public static int $allowCache;
    public static array $JS = array();

    //Empty array = allow all
    public static array $compatiblePages;

    protected string $init_path;
    protected \ModulesManager\ModulesManager $mm_instance;

    private \PDO $db;

    public function __construct(string $init_path, \PDO $db, \ModulesManager\ModulesManager &$mm){
        $this->db = $db;
        $this->init_path = $init_path;
        $this->mm_instance = $mm;
    }

    public function getName() : string { return self::$name; }
    public function getVIewPath() : string { return self::$view_path; }
    public function getPDO() : \PDO { return $this->db; }
    public function getInitPath() : string { return $this->init_path; }

    public static abstract function getDefaultConstructorArguments(\PDO $db) : array;
    public static abstract function canBeInitializedWithDefault(\PDO $db) : bool;
    public abstract function render(bool $editing_mode=false) : string;
}