<?php
namespace PageBuilders; 

class ThemeBuilder {
    protected string $theme_name;
    protected string $namespace;

    const CACHE_DYN = 1;
    const CACHE_SEMISTATIC = 5;
    const CACHE_STATIC = 1440;
    const CACHE_NAMES = array(self::CACHE_DYN => "CACHE_DYN", self::CACHE_SEMISTATIC => "CACHE_SEMISTATIC", self::CACHE_STATIC => "CACHE_STATIC");

    public function __construct(string $theme_name, string $namespace="PageBuilders") {
        $this->theme_name = ucfirst($theme_name);
        $this->namespace = $namespace;
    }

    public function __call($name, $args){
        if (class_exists($this->namespace . str_replace(" ", "", '\ ') . $this->theme_name .  $name)){
            $cn = $this->namespace . str_replace(" ", "", '\ ') . $this->theme_name . $name;
            return new $cn(...$args);
        }else if (class_exists($this->namespace . str_replace(" ", "", '\ ') .  $name)){
            $cn = $this->namespace . str_replace(" ", "", '\ ') . $name;
            return new $cn(...$args);
        }else if (class_exists($name)){
            $cn = $name;
            return new $cn(...$args);
        }
        throw new \DiamondException("Class name invalid. No class found for " . $this->namespace . str_replace(" ", "", '\ ') . $this->theme_name .  $name, "native$999");
    }

    public static function renderFromCacheIfPossible($tbname, $cache_type){
        $cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . self::CACHE_NAMES[$cache_type] . "/", $cache_type);
        if (($tb = $cacheinstance->read(mb_strtolower($tbname) . ".dcms")) != false){
            \ob_start();
            require_once(ROOT . "views/themes/default/include/header_admin.inc");
            echo $tb;
            require_once(ROOT . "views/themes/default/include/footer_admin.inc");
            return \ob_get_clean();
        }
        return false;
    }

    public static function startCache($tbname, $cache_type, $callBack, $shouldrenderasview=true) : void{
        $cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . self::CACHE_NAMES[$cache_type] . "/", $cache_type);
        if (($tb = $cacheinstance->read(mb_strtolower($tbname) . ".dcms")) != false){
            if (($serialcontroleur = $cacheinstance->read(mb_strtolower($tbname) . ".serialController.dcms")) != false){
                $GLOBALS['controleur_def']->unSerialize($serialcontroleur);
            }
            $GLOBALS['controleur_def']->loadAsView($tb, true);
        }else if (is_callable($callBack)){
            $GLOBALS['DIAMOND_CACHE_PROCESSING'] = true;
            \ob_start();
            $refFunction = new \ReflectionFunction($callBack);
            $args = array();
            foreach($refFunction->getParameters() as $p){
                if (isset($GLOBALS[$p->getName()]))
                    array_push($args, $GLOBALS[$p->getName()]);
                else
                    throw new \DiamondException("Argument name is not a global variable.", "native$999");
            }
            $callBack(...$args);
            $cacheinstance->write(mb_strtolower($tbname) . ".dcms", $buffer=\ob_get_clean());
            $cacheinstance->write(mb_strtolower($tbname) . ".serialController.dcms", $GLOBALS['controleur_def']->serialize());
            $GLOBALS['DIAMOND_CACHE_PROCESSING'] = false;
            if ($shouldrenderasview)
                $GLOBALS['controleur_def']->loadAsView($buffer, true);
        }
    }

    public static function clearCache($tbname, $cache_type){
        $cacheinstance = new \DiamondCache(ROOT . "tmp/AdminBuilder/" . self::CACHE_NAMES[$cache_type] . "/", $cache_type);
        return $cacheinstance->clean(true);
    }

    public static function FA(string $fa_name) : string {
        return '<i class="fa '. $fa_name . ' fa-fw"></i>';
    }
}