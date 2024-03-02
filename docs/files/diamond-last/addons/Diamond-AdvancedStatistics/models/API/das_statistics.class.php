<?php 


require_once(ROOT . "addons/Diamond-AdvancedStatistics/models/corestats.php");
class das_statistics extends DiamondAPI {

    private \DiamondAdvancedStatistics\CoreStats $corestats;
    private array $das_config;

    public function __construct(array $paths, PDO $pdo, Controleur $controleur, int $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        try {
            $this->corestats = new DiamondAdvancedStatistics\CoreStats($this->getPDO());
        }catch (Throwable $e){
            if ($e->getCode() == "42S02" && file_exists(ROOT . "addons/Diamond-AdvancedStatistics/installed.dcms"))
                unlink(ROOT . "addons/Diamond-AdvancedStatistics/installed.dcms");
            else
                throw $e;
        }
        $this->das_config = cleanIniTypes(parse_ini_file(ROOT . 'addons/Diamond-AdvancedStatistics/config.ini', true));
        $this->params_needed = array(
            "get_registerhit" => array(),   
            "get_totalHitsByDay" => array(),
            "get_bestReferer" => array(),
            "get_bestPages" => array(),
            "set_reset" => array(),
            "set_editConfig" => array(),
        );
    }


    public function get_registerhit(){
        if (!isset($this->args['internal_path']))
            $this->args['internal_path'] = "";
        try {
            DiamondAdvancedStatistics\CoreStats::newHit($this->getPDO(), $this->args['internal_path'], (isset($_SESSION['user']) && $_SESSION['user'] instanceof \User) ? $_SESSION['user']->getId() : null,
            isset($this->args['HTTP_USER_AGENT']) ? $this->args['HTTP_USER_AGENT'] : null, isset($this->args['HTTP_REFERER']) ? $this->args['HTTP_REFERER'] : null, isset($this->args['REQUEST_TIME']) ? $this->args['REQUEST_TIME'] : null);
        }catch (Throwable $e){
            if ($e->getCode() == "42S02" && file_exists(ROOT . "addons/Diamond-AdvancedStatistics/installed.dcms"))
                unlink(ROOT . "addons/Diamond-AdvancedStatistics/installed.dcms");
            else
                throw $e;
        }
        
        return $this->formatedReturn(1);
    }

    public function get_totalHitsByDay(){
        if ($this->level < 2)
            throw new Exception("Forbidden access", 706);
        return $this->formatedReturn($this->corestats->totalHitsByDayOrMonth());
    }

    public function get_bestReferer(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);
        return $this->formatedReturn($this->corestats->bestReferer(true, true, true, (isset($this->das_config['should_count_admin']) ? $this->das_config['should_count_admin'] : true)));
    }

    public function get_bestPages(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);
        return $this->formatedReturn($this->corestats->bestPages(true, true, (isset($this->das_config['should_count_admin']) ? $this->das_config['should_count_admin'] : true)));
    }

    public function set_reset(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);
        try{
            if (simplifySQL\delete($this->getPDO(), "d_statistics_hits") != true)
                throw new DiamondException("Error while purging d_statistics_hits", "341b");
        }catch (Exception $e){
            throw new DiamondException("Error while purging d_statistics_hits (2)", "341b");
        }
        return $this->formatedReturn(1);
    }

    public function set_editConfig(){
        if ($this->level < 4)
            throw new Exception("Forbidden access", 706);
        $this->setConfig(ROOT . 'addons/Diamond-AdvancedStatistics/config.ini', $this->args, false, true);
        return $this->formatedReturn(1);
    }

}
