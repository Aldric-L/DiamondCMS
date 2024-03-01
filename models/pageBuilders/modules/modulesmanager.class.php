<?php 

namespace ModulesManager;

abstract class ModulesManager{

    protected array $modulesavailable = array();
    protected array $loadedModules = array();
    protected array $loadedModulesNames = array();
    protected string $page_name;
    protected bool $is_initialised = false;
    protected array $default_conf;

    const CACHE_DYN = 1;
    const CACHE_SEMISTATIC = 5;
    const CACHE_STATIC = 1440;
    const CACHE_NAMES = array(self::CACHE_DYN => "CACHE_DYN", self::CACHE_SEMISTATIC => "CACHE_SEMISTATIC", self::CACHE_STATIC => "CACHE_STATIC");


    public function getPageName() : string { return $this->page_name; }

    /**
     * @param array $modulesavailable : tableau des noms et des paths modules initialisés
     * @param string $page_name : nom de la page
     * @param array $default_conf : tableau des array("mod_name" => , "parameters" => array())
     */
    public function __construct(\PDO $db, array $modulesavailable, string $page_name, $default_conf=null){
        $this->modulesavailable = $modulesavailable;
        $this->page_name = $page_name;

        if ($default_conf != null && is_array($default_conf))
            return $this->init($db, $default_conf);
        else
            return;
    }

    public final function init(\PDO $db, array $default_conf){
        if ($this->is_initialised)
            throw new \DiamondException("Cannot re-initialize ModulesManager", "native$803");

        $this->writeDefaultConfig($default_conf);
        $this->default_conf = $default_conf;
        $this->loadedModulesNames = array();

        if (\file_exists(ROOT . "config/modules/" . $this->page_name . "/config.json")){
            $default_conf = array();
            $dc = \json_decode(\file_get_contents(ROOT . "config/modules/" . $this->page_name . "/config.json"), true);
            foreach($dc as $m){
                array_push($default_conf, array("mod_name" => $m));
            }
        }
        $modulesavailablename = array();
        foreach ($this->modulesavailable as $m){
            array_push($modulesavailablename, $m['name']);
        }

        foreach ($default_conf as $raw_mod){
            if (isset($raw_mod['mod_name']) && \in_array($raw_mod['mod_name'], $modulesavailablename)){
                try{
                    if (!in_array($this->page_name, (str_replace(" ", "", "ModulesManager\ ") . $raw_mod['mod_name'])::$compatiblePages) && !empty((str_replace(" ", "", "ModulesManager\ ") . $raw_mod['mod_name'])::$compatiblePages))
                        throw new \DiamondException("Trying to load an uncompatible module", "native$801");
                        
                    $shouldbeincache = false;
                    $name = str_replace(" ", "", "ModulesManager\ ") . $raw_mod['mod_name'];
                    if ($name::$allowCache != 0){
                        $cacheinstance = new \DiamondCache(ROOT . "tmp/ModulesManager/" . self::CACHE_NAMES[$name::$allowCache] . "/" . $this->page_name . "/" , $name::$allowCache);
                        if (($incache = $cacheinstance->read(mb_strtolower($raw_mod['mod_name']) . ".dcms")) != false)
                            $shouldbeincache = true;
                    }
                    
                    if (!$shouldbeincache){
                        if (isset($raw_mod['parameters']) && is_array($raw_mod['parameters'])){
                            array_push($this->loadedModules, new $name(...array_merge(array($this->getPathForModuleByName($raw_mod['mod_name']), $db, $this), $raw_mod['parameters'])));
                            array_push($this->loadedModulesNames, $raw_mod['mod_name']);
                        }else if ((str_replace(" ", "", "ModulesManager\ ") . $raw_mod['mod_name'])::canBeInitializedWithDefault($db)) {
                            $parameters = array($this->getPathForModuleByName($raw_mod['mod_name']), $db, $this);
                            $parameters = array_merge($parameters, (str_replace(" ", "", "ModulesManager\ ") . $raw_mod['mod_name'])::getDefaultConstructorArguments($db));
                            array_push($this->loadedModules, new $name(...$parameters));
                            array_push($this->loadedModulesNames, $raw_mod['mod_name']);
                        }
                    }else {                            
                        array_push($this->loadedModules, array("name" => $raw_mod['mod_name'], "cache" => $incache, 
                        "class_name"=>(str_replace(" ", "", "ModulesManager\ ") . $raw_mod['mod_name']), 
                        "parameters" => (isset($raw_mod['parameters']) && is_array($raw_mod['parameters'])) ? array_merge(array($this->getPathForModuleByName($raw_mod['mod_name']), $db, $this), $raw_mod['parameters']) : array_merge(array($this->getPathForModuleByName($raw_mod['mod_name']), $db, $this), (str_replace(" ", "", "ModulesManager\ ") . $raw_mod['mod_name'])::getDefaultConstructorArguments($db))));
                        array_push($this->loadedModulesNames, $raw_mod['mod_name']);
                    }
                    
                }catch (\Error $e){
                    //var_dump($e); die;
                    if (defined("DEV_MODE") && DEV_MODE)
                        throw new \DiamondException("Module required unavailable or bad initialisation (b)", "native$800");
                }catch (\DiamondException $e){
                    //var_dump($e); die;
                    if (defined("DEV_MODE") && DEV_MODE)
                        throw new \DiamondException("Module required unavailable or bad initialisation (c)", "native$800");
                }catch (\Exception $e){
                    //var_dump($e); die;
                    if (defined("DEV_MODE") && DEV_MODE)
                        throw new \DiamondException("Module required unavailable or bad initialisation (d)", "native$800");                
                }
            }else {
                if (defined("DEV_MODE") && DEV_MODE)
                    throw new \DiamondException("Module required unavailable or bad initialisation (a)", "native$800");
            }
        }
        
        $this->is_initialised = true;
        return $this;
    }

    public final function getMyConfig(string $module_name, string $conf_name){
        $module = str_replace(" ", "", "ModulesManager\ ") . $module_name;

        if (!class_exists($module))
            throw new \DiamondException("Module required unavailable or bad initialisation (e)", "native$800");

        if (\file_exists(ROOT . "config/modules/" . $this->page_name . "/modules_config" . "/"  . ($module)::$name . "/" . $conf_name))
            return \file_get_contents(ROOT . "config/modules/" . $this->page_name . "/modules_config" . "/"  . ($module)::$name . "/" . $conf_name);
        else 
            return null;
    }

    public final function setMyConfig(string $module_name, string $conf_name, string $conf_to_write){
        $module = str_replace(" ", "", "ModulesManager\ ") . $module_name;

        if (!class_exists($module))
            throw new \DiamondException("Module required unavailable or bad initialisation (e)", "native$800");

        if (!\file_exists(ROOT . "config/modules/" . $this->page_name . "/modules_config" . "/"  . ($module)::$name . "/"))
            \mkdir(ROOT . "config/modules/" . $this->page_name . "/modules_config" . "/"  . ($module)::$name . "/", 0777, true);
        return \file_put_contents(ROOT . "config/modules/" . $this->page_name . "/modules_config" . "/"  . ($module)::$name . "/" . $conf_name, $conf_to_write);
    }

    public final function deleteOnesModuleCache(string $module_name) : void {
        $module = str_replace(" ", "", "ModulesManager\ ") . $module_name;

        if (!class_exists($module))
            throw new \DiamondException("Module required unavailable or bad initialisation (e)", "native$800");
        
        if (($module)::$allowCache != 0){
            $cacheinstance = new \DiamondCache(ROOT . "tmp/ModulesManager/" . self::CACHE_NAMES[($module)::$allowCache] . "/" . $this->page_name . "/" , ($module)::$allowCache);
            if (($incache = $cacheinstance->read(mb_strtolower(($module)::$name) . ".dcms")) != false)
                $cacheinstance->write(mb_strtolower(($module)::$name) . ".dcms", null);
        }
                
    }

    protected final function getPathForModuleByName($mod_name) : string {
        foreach ($this->modulesavailable as $m){
            if ($m['name'] == $mod_name)
                return $m['path'];
        }
    }

    public final function deleteOneModule(string $module_name, int $module_key) : void{
        $loadedModulesNames = array();
        if ($this->is_initialised){
            foreach ($this->loadedModulesNames as $key => $m){
                if ($m == $module_name && $key == $module_key){
                    unset($this->loadedModules[$key]);
                    unset($this->loadedModulesNames[$key]);
                }else{
                    \array_push($loadedModulesNames, $m::$name);
                }
            }
        }else {
            foreach (\json_decode(\file_get_contents(ROOT . "config/modules/" . $this->page_name . "/config.json"), true) as $key => $m){
                if (!($m == $module_name && $key == $module_key))
                    \array_push($loadedModulesNames, $m);
            }
        }
        
        $this->rewriteConfig($loadedModulesNames, true);
    }

    public final function changeModuleKeyInConfig(string $module_name, int $cur_module_key, int $new_module_key) : void {
        if (sizeof($confignames = \json_decode(\file_get_contents(ROOT . "config/modules/" . $this->page_name . "/config.json"), true)) < max($cur_module_key, $new_module_key))
            throw new \DiamondException("Error, vector subscript out of range.");


        function swap($tab, $i1, $i2){ $tmp = $tab[$i1]; $tab[$i1] = $tab[$i2]; $tab[$i2] = $tmp; return $tab;  }

        function reorder($i1, $i2, $tab) {
            if ($i1 > $i2) { $tmp = $i1; $i1 = $i2; $i2 = $tmp; }
            for ($i = $i1; $i < $i2; $i -= 1) {
              return swap($tab,$i,$i2);
            }
        }

        $this->rewriteConfig(reorder($cur_module_key, $new_module_key, $confignames), true);        
    }

    public final function addOneModule(\PDO $db, string $module_name) : void {
        if (!in_array($this->page_name, (str_replace(" ", "", "ModulesManager\ ") . $module_name)::$compatiblePages) && !empty((str_replace(" ", "", "ModulesManager\ ") . $module_name)::$compatiblePages))
            throw new \DiamondException("Trying to load an uncompatible module", "native$801");

        $cname = \str_replace(" ", "", "ModulesManager\ ") . $module_name;
        $parameters = array($this->getPathForModuleByName($module_name), $db, $this);
        $parameters = \array_merge($parameters, $cname::getDefaultConstructorArguments($db));
        \array_push($this->loadedModules, new $cname(...$parameters));
        \array_push($this->loadedModulesNames, $module_name);
        $loadedModulesNames = array();
        foreach($this->loadedModules as $m){
            \array_push($loadedModulesNames, $m::$name);
        }

        $this->rewriteConfig($loadedModulesNames, false);
    }

    public final function getAvailableModulesNamesForThisMM() : array{
        $modules = array();
        foreach ($this->modulesavailable as $m){
            if (in_array($this->page_name, (str_replace(" ", "", "ModulesManager\ ") . $m['name'])::$compatiblePages) || empty((str_replace(" ", "", "ModulesManager\ ") . $m['name'])::$compatiblePages))
                \array_push($modules, $m['name']);
        }
        return $modules;
    }

    public abstract function renderModules(\Controleur &$controleur_def, bool $editing_mode=false) : string;

    public static function isThereAModulesManager(string $page_name) : bool {
        return (\file_exists(ROOT . "config/modules/" . $page_name . "/default.json"));
    }

    private function rewriteConfig(array $loadedModulesNames, bool $erase) : void{
        if (!\file_exists(ROOT . "config/modules/" . $this->page_name))
            \mkdir(ROOT . "config/modules/" . $this->page_name);
        if (!$erase && \file_exists(ROOT . "config/modules/" . $this->page_name . "/config.json"))
            $old_conf = \json_decode(\file_get_contents(ROOT . "config/modules/" . $this->page_name . "/config.json"), true); 
        else
            $old_conf = array();
        \file_put_contents(ROOT . "config/modules/" . $this->page_name . "/config.json", json_encode(array_merge($old_conf, $loadedModulesNames)));
    }

    private function writeDefaultConfig(array $default_conf) : void {
        $modulesNames = array();
        foreach ($default_conf as $raw_mod){
            \array_push($modulesNames, $raw_mod['mod_name']);
        }

        if (!\file_exists(ROOT . "config/modules/" . $this->page_name))
            \mkdir(ROOT . "config/modules/" . $this->page_name, 0777, true);
            
        if (!\file_exists(ROOT . "config/modules/" . $this->page_name . "/config.json"))
            \file_put_contents(ROOT . "config/modules/" . $this->page_name . "/config.json", json_encode($modulesNames));

        \file_put_contents(ROOT . "config/modules/" . $this->page_name . "/default.json", json_encode($modulesNames));

    }

}