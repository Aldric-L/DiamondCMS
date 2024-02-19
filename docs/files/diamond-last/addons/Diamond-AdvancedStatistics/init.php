<?php 
if (file_exists(ROOT . 'addons/Diamond-AdvancedStatistics/disabled.dcms')){
    define("DStats", false);
}else {
    define("DStats", true);
}

$script_path = get_required_files()[sizeof(get_required_files())-1];
$sep = "/";
$pths = explode($sep, $script_path);
if (sizeof($pths) === 1){
    $sep = str_replace(" ", "", "\ ");
    $pths = explode($sep, $script_path);
}
if (sizeof($pths) < 2){
    throw new DiamondException("Unable to find root path.", "Diamond-AdvancedStatistics$110");
}else {
    array_pop($pths);
    if ($pths[sizeof($pths)-1] != "Diamond-AdvancedStatistics"){
        $new_pths = $pths;
        $new_pths[sizeof($pths)-1] = "Diamond-AdvancedStatistics";
        $orgpath = implode($sep, $pths);
        $newpath = implode($sep, $new_pths);
        try {
            rename($orgpath, $newpath);
        }catch (\Throwable $e) {
            throw new DiamondException("Unable to find root path. (2)", "Diamond-AdvancedStatistics$110");
        }
        header('Location: ' . LINK);
        die;
    }
}
//18-12-2021 @author Aldric L.
define("DStatsVersion", "1.0");
if (DStats){
    if (!file_exists(__DIR__  ."/installed.dcms")){
        try {
            simplifySQL\select($this->bddConnexion(), true, "d_statistics_hits ", "*", array(array("id", "=", 0)));
        } catch (\Exception $e) {
            if ($e->getCode() == "42S02"){
                $this->bddConnexion()->exec(file_get_contents(ROOT . 'addons/Diamond-AdvancedStatistics/install_files/core.sql'));
            }
        }
        file_put_contents(__DIR__  ."/installed.dcms", "true");
    }

    try {
        if (!file_exists(__DIR__  ."/config.ini")){
            $das_config = array( 
                "should_count_admin" => true,
                "async"=> true
            );
            $ini = new \ini (__DIR__  ."/config.ini", 'Configuration DiamondCMS : Addon Diamond-AdvancedStatistics');
            $ini->ajouter_array($das_config);
            $ini->ecrire(true);
        }else {
            $das_config = cleanIniTypes(parse_ini_file(__DIR__  ."/config.ini", true));
        }
    }catch (\Throwable $e) {
        throw new DiamondException("Unable to access config (" . ($e->getCode()) . ")", "Diamond-AdvancedStatistics$110");
    }
    
    if (isset($das_config['async']) && !$das_config['async']){
        require_once(ROOT . "addons/Diamond-AdvancedStatistics/models/corestats.php");

        $internal_path = "";
        $param = explode('/',$_GET['p']);
        foreach ($param as $p){ if (!empty($p)){ $internal_path .= $p . "/"; }}
        try{
            if ($param[0] != "getimage" && $param[0] != "getprofileimg" && $param[0] != "api" && $param[0] != "API" && $param[0] != "installation"
            && ($param[0] != "serveurs" || !(isset($param[1]) && $param[1] == "json"))
            && (!isset($param[1]) || $param[1] != "admin_iframe")
            && (!isset($param[3]) || $param[3] != "ADMIN-IFRAME")
            /*&& (!isset($das_config['should_count_admin']) || $das_config['should_count_admin'] || $param[0] != "admin")*/){
                DiamondAdvancedStatistics\CoreStats::newHit(
                    $this->bddConnexion(), $internal_path, 
                    ((isset($_SESSION['user']) && $_SESSION['user'] instanceof \User) ? $_SESSION['user']->getId() : null),
                    ((isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null), 
                    ((isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : null), 
                    ((isset($_SERVER['REQUEST_TIME'])) ? $_SERVER['REQUEST_TIME'] : null)
                );
            }
        }catch(Exception $e){}
    }else {
        $this->loadJSAddon(LINK . "addons/Diamond-AdvancedStatistics/views/js/registerHit.js" );
    }
        
}
