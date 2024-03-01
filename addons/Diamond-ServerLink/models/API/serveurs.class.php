<?php        

class serveurs extends DiamondAPI {
    public function __construct(array $paths, PDO $pdo, Controleur $controleur, int $level){
        parent::__construct($paths, $pdo, $controleur, $level);
        $this->params_needed = array(
            "get_testQueryRaw" => array("host", "queryport", "game"),
            "get_testRconRaw" => array("host", "rconport", "password", "game"),
            "set_addserver" => array("name", "desc", "host", "queryport", "rconport", "password", "game", "version"),
            "get_diagnostic" => array("id"),
            "set_oneserverconfig" => array("id"),
            "set_delserver" => array("id"),
            "set_execOnServer" => array("id", "cmd"),
            "get_getInfos" => array(),
            "get_getPlayers" => array(),
            "get_serverState" => array(),
        );
    }

    public function get_testQueryRaw(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);

        if (!is_numeric($this->args['queryport']))
            throw new DiamondException("Queryport must be integer.", 701);

            
        if ($this->args['game'] == "Minecraft JSONAPI"){
            if (!isset($this->args["password"]))
                throw new DiamondException("Missing JSONAPI Password.", 701);
            $r = "";
            try{
                $Query = new DServerLink\JSONAPI($this->args['host'], intval($this->args['queryport']), "Diamond-ServerLink", $this->args["password"], "DiamondSALT");
                $rtrn = json_decode($Query->call("getServer"), true);
                if (is_array($rtrn) && !empty($rtrn) && $rtrn[0]['result'] == "error" && isset($rtrn[0]['error']))
                    $r = "JSONAPI Error : " . $rtrn[0]['error']['message']; 
                else if (!(is_array($rtrn) && !empty($rtrn) && $rtrn[0]['result'] == "success" && isset($rtrn[0]['success'])))
                    $r = "Unable to reach Minecraft Server, using JSONAPI"; 
            }catch( Throwable $e ){
                $r = "Unable to reach Minecraft Server, using JSONAPI (Throwable fund)"; 
            }finally {
                if ($r == "")
                    return $this->formatedReturn("Connection archived");  
                else 
                    return $this->formatedReturn($r);    
            }
        }else {
            if ($this->args['game'] == "Minecraft" || $this->args['game'] == "Minecraft-Java" || $this->args['game'] == "Minecraft-MPCE")
                $Query = new DServerLink\MinecraftQuery\MinecraftQuery();
            else 
                $Query = new DServerLink\SourceQuery\SourceQuery();

            try{
                $Query->Connect( $this->args['host'],  intval($this->args['queryport']), 4);
                $r = $Query->GetInfo();
            }catch( Exception $e ){
                $r = $e->getMessage();
            }finally{
                if ($this->args['game'] != "Minecraft" && $this->args['game'] != "Minecraft-Java" && $this->args['game'] != "Minecraft-MPCE")
                    $Query->disconnect();
            }

            if (is_array($r))
                return $this->formatedReturn("Connection archived");        
            else 
                return $this->formatedReturn($r);        

            return $this->formatedReturn($r);
        }
        
    }

    public function get_testRconRaw(){
        if ($this->level < 4)
            throw new DiamondException("Forbidden access", 706);

        if (!is_numeric($this->args['rconport']))
            throw new DiamondException("Rconport must be integer.", 701);

        if ($this->args['game'] == "Minecraft JSONAPI"){
            if (!isset($this->args["password"]))
                throw new DiamondException("Missing JSONAPI Password.", 701);

            $r = "";
            try{
                $Query = new DServerLink\JSONAPI($this->args['host'], intval($this->args['rconport']), "Diamond-ServerLink", $this->args["password"], "DiamondSALT");
                $rtrn = json_decode($Query->call("getServer"), true);
                if (is_array($rtrn) && !empty($rtrn) && $rtrn[0]['result'] == "error" && isset($rtrn[0]['error']))
                    $r = "JSONAPI Error : " . $rtrn[0]['error']['message']; 
                else if (!(is_array($rtrn) && !empty($rtrn) && $rtrn[0]['result'] == "success" && isset($rtrn[0]['success'])))
                    $r = "Unable to reach Minecraft Server, using JSONAPI"; 
            }catch( Throwable $e ){
                $r = "Unable to reach Minecraft Server, using JSONAPI (Throwable fund)"; 
            }finally {
                if ($r == "")
                    return $this->formatedReturn("Connection archived");  
                else 
                    return $this->formatedReturn($r);    
            }
        }else {
            $Query = new DServerLink\SourceQuery\SourceQuery();
            $r="";
            try{
                $Query->Connect( $this->args['host'],  intval($this->args['rconport']), 4);		
                $Query->SetRconPassword($this->args['password']);
                $Query->Rcon( "say Installation de Diamond-ServerLink : verification du bon fonctionnement de la configuration." );
            }catch( Exception $e ){
                $r = $e->getMessage();
            }catch (Error $e){
                $r = "Erreur interne grave. Veuillez contacter le support de DiamondCMS pour : " . $e->getMessage();
            }finally{
                if ($this->args['game'] != "Minecraft" && $this->args['game'] != "Minecraft-Java" && $this->args['game'] != "Minecraft-MPCE")
                    $Query->disconnect();
            }
        }
        
        if ($r == "")
            return $this->formatedReturn("Connection archived");        
        else 
            return $this->formatedReturn($r);        

        return $this->formatedReturn($r);   
    }

    public function set_addserver(){
        $img = $this->processImgFromDIC();

        $cm = new DServerLink\ConfigManager();
        $newconf = $cm->getConfig();
        $nbservers = 0;
        foreach($newconf as $c){
            $nbservers = $nbservers+1;
        }

        $en = (isset($this->args['enabled']) && is_true($this->args['enabled']) === false) ? false : true;
        $newconf[$nbservers+1] = array( "id" => $nbservers+1, "game" => $this->args['game'], "version" => $this->args['version'], 
        "name" => $this->args['name'], "desc" => $this->args['desc'], "img" => $img,
        "host" => $this->args['host'], "queryport" => $this->args['queryport'], "port" => $this->args['rconport'],
        "password" => $this->args['password'], "enabled" => $en);
        
        $cm->editConfig($newconf);
        return $this->formatedReturn(1);   
    }

    public function get_diagnostic(){
        $cm = new DServerLink\ConfigManager();
        $config_serveur = $cm->getConfig();

        if (!array_key_exists($this->args['id'], $config_serveur))
            throw new DiamondException("Bad arguments, unable to find server by id in config", 701);
        
        $id = $this->args['id'];
        $r = "";

        if (sizeof($this->args) > 1){
            foreach ($this->args as $key => $arg) {
                if (array_key_exists($key, $config_serveur[$id])){
                    $config_serveur[$id][$key] = $arg;
                }
            }
        }

        if ((is_bool($config_serveur[$id]['enabled']) && !$config_serveur[$id]['enabled']) || (is_string($config_serveur[$id]['enabled']) && $config_serveur[$id]['enabled'] == "false"))
            return $this->formatedReturn("Disabled");   

        if ($config_serveur[$id]['game'] == "Minecraft JSONAPI"){
            try{
                $Query = new DServerLink\JSONAPI($config_serveur[$id]['host'], intval($config_serveur[$id]['port']), "Diamond-ServerLink", $config_serveur[$id]["password"], "DiamondSALT");
                $rtrn = json_decode($Query->call("getServer"), true);
                if (is_array($rtrn) && !empty($rtrn) && $rtrn[0]['result'] == "error" && isset($rtrn[0]['error']))
                    $r = $rtrn[0]['error']['message']; 
                else if (!(is_array($rtrn) && !empty($rtrn) && $rtrn[0]['result'] == "success" && isset($rtrn[0]['success'])))
                    $r = "Unable to reach Minecraft Server, using JSONAPI"; 
            }catch( Throwable $e ){
                $r = "Unable to reach Minecraft Server, using JSONAPI (Throwable fund)"; 
            }
            
        }else {
            if ($config_serveur[$id]['game'] == "Minecraft" || $config_serveur[$id]['game'] == "Minecraft-Java" || $config_serveur[$id]['game'] == "Minecraft-MPCE")
                $Query = new DServerLink\MinecraftQuery\MinecraftQuery();
            else 
                $Query = new DServerLink\SourceQuery\SourceQuery();

            try{
                $Query->Connect( $config_serveur[$id]['host'],  intval($config_serveur[$id]['queryport']), 4);
                $r = $Query->GetInfo();
            }catch( Exception $e ){
                $r = $e->getMessage();
            }catch( Error $e ){
                $r = $e->getMessage();
            }finally{
                if ($config_serveur[$id]['game'] != "Minecraft" && $config_serveur[$id]['game'] != "Minecraft-Java" && $config_serveur[$id]['game'] != "Minecraft-MPCE")
                    $Query->disconnect();
            }

            if (!is_array($r))
                return $this->formatedReturn("Query Error : " . $r);

            $RCon = new DServerLink\SourceQuery\SourceQuery();
            $r="";
            try{
                $RCon->Connect( $config_serveur[$id]['host'],  intval($config_serveur[$id]['port']), 4);		
                $RCon->SetRconPassword($config_serveur[$id]['password']);
                $RCon->Rcon( "say Diagnostic de Diamond-ServerLink : verification du bon fonctionnement de la configuration." );
            }catch( Exception $e ){
                    $r = $e->getMessage();
            }catch (Error $e){
                    $r = "Erreur interne grave. Veuillez contacter le support de DiamondCMS pour : " . $e->getMessage();
            }finally{
                if ($config_serveur[$id]['game'] != "Minecraft" && $config_serveur[$id]['game'] != "Minecraft-Java" && $config_serveur[$id]['game'] != "Minecraft-MPCE")
                    $RCon->disconnect();
            }
        }
            
        if ($r == "")
            return $this->formatedReturn("Connection archived");
        else if ($config_serveur[$id]['game'] == "Minecraft JSONAPI")
            return $this->formatedReturn("JSONAPI Error : " . $r);        
        else 
            return $this->formatedReturn("RCon Error : " . $r);
        
    }

    public function set_oneserverconfig(){
        if ($this->level < 5)
            throw new DiamondException("Forbidden access", 706);
        if ($this->args == null || empty($this->args) || !isset($this->args['id']))
            throw new DiamondException("Missing arguments", 701);
        if (!defined("DServerLink") || DServerLink != true )
            throw new DiamondException("Addon missing", 707);
        
        $img = $this->processImgFromDIC();
        $this->args = $_POST;
        $this->args['img'] = $img;

        $a = $this->args;
        unset($this->args);
        $this->args[intval($a['id'])] = $a;
        $this->setConfig(ROOT."config/" . "serveurs.ini", $this->args, false, true);
        return $this->formatedReturn(1);
    }

    public function set_delserver(){
        if ($this->level < 5)
            throw new DiamondException("Forbidden access", 706);
        if ($this->args == null || empty($this->args))
            throw new DiamondException("Missing arguments", 701);
        if (!defined("DServerLink") || DServerLink != true )
            throw new DiamondException("Addon missing", 707);

        $newconf = $this->getIniConfig(ROOT."config/serveurs.ini");
        $is_last = intval($this->args['id']) === sizeof($newconf) ? true : false;
        unset($newconf[intval($this->args['id'])]);
        
        if (!$is_last){
            foreach ($newconf as $k => $nc){
                if ($nc['id'] > $this->args['id']){
                    $newconf[$k]['id'] = $newconf[$k]['id']-1;
                    $newconf[$k-1] = $newconf[$k];
                }
            }

            unset($newconf[sizeof($newconf)]);
        }
        //Il faut bien penser aussi aux conséquences, comme la suppression des commandes associées
        simplifySQL\delete($this->getPDO(), "d_boutique_cmd", array(array("server", "=", $this->args['id'])));
 
        $this->setConfig(ROOT."config/serveurs.ini", $newconf, true);
        return $this->formatedReturn(1);

    }


    public function set_execOnServer(){
        $cm = new DServerLink\ConfigManager();
        $config_serveur = $cm->getConfig();

        if (!array_key_exists($this->args['id'], $config_serveur))
            throw new DiamondException("Bad arguments, unable to find server by id in config", 701);
        
        $id = $this->args['id'];
        $cmd = $this->args['cmd'];

        //On initialise la connexion RCON
        $controleur_def = $this->getControleur();
        $rcon = new DServerLink\RCon($controleur_def, $cm, true);
        $rcon->connect($id);
        if (!empty($rcon->getErrors())){
            $rcon->disconnect($id);
            foreach ($rcon->getErrors() as $e){
                $e_string = (string)$e;
                if ($e_string[0] == '4')
                    throw new DiamondException("Internal Error", "Diamond-ServerLink$" . $e_string);
                else
                    throw new DiamondException("Internal Error", $e);
            }
        }

        $rtn = $rcon->execOnServer($id, $cmd);
        $rcon->disconnect($id);
        if (!empty($rcon->getErrors())){
            foreach ($rcon->getErrors() as $e){
                $e_string = (string)$e;
                if ($e_string[0] == '4')
                    throw new DiamondException("Internal Error", "Diamond-ServerLink$" . $e_string);
                else
                    throw new DiamondException("Internal Error", $e);
            }
        }

        return $this->formatedReturn($rtn);
    }


    public function get_getInfos(){
        $servers_link = new DServerLink\Query($this->getControleur(), new DServerLink\ConfigManager());
        $servers_link->connect();
        if (!empty($servers_link->getErrors())){
            $servers_link->disconnect();
            foreach ($servers_link->getErrors() as $e){
                $e_string = (string)$e;
                if ($e_string[0] == '4')
                    throw new DiamondException(null, "Diamond-ServerLink$" . $e_string);
                else
                    throw new DiamondException(null, $e);
            }
        }

        if (isset($this->args['id']) && is_numeric($this->args['id']))
            $servers = $servers_link->getInfos(intval($this->args['id']));
        else 
            $servers = $servers_link->getInfos();

        if (!empty($servers_link->getErrors())){
            $servers_link->disconnect();
            foreach ($servers_link->getErrors() as $e){
                $e_string = (string)$e;
                if ($e_string[0] == '4')
                    throw new DiamondException(null, "Diamond-ServerLink$" . $e_string);
                else
                    throw new DiamondException(null, $e);
            }
        }
        
        $servers_link->disconnect();
        return $this->formatedReturn($servers);
    }

    public function get_getPlayers(){
        $servers_link = new DServerLink\Query($this->getControleur(), new DServerLink\ConfigManager());
        $servers_link->connect();
        if (!empty($servers_link->getErrors())){
            $servers_link->disconnect();
            foreach ($servers_link->getErrors() as $e){
                throw new DiamondException(null, $e);
            }
        }

        if (isset($this->args['id']) && is_numeric($this->args['id']))
            $servers = $servers_link->getPlayers(intval($this->args['id']));
        else 
            $servers = $servers_link->getPlayers();

        if (!empty($servers_link->getErrors())){
            $servers_link->disconnect();
            foreach ($servers_link->getErrors() as $e){
                $e_string = (string)$e;
                if ($e_string[0] == '4')
                    throw new DiamondException(null, "Diamond-ServerLink$" . $e_string);
                else
                    throw new DiamondException(null, $e);
            }
        }
        
        $servers_link->disconnect();
        return $this->formatedReturn($servers);
    }

    public function get_serverState(){
        $pvcache = $this->getCacheInstance(self::CACHE_DYN);
        $cache = $pvcache->read("serverStateAPI.dcms");
        if ($cache == false){
            $servers_link = new DServerLink\Query($this->getControleur(), new DServerLink\ConfigManager());
            $servers_link->connect();
            $servers = $servers_link->getInfos();
            $servers_link->disconnect();
            $width = 350;
            if (isset($this->args['width']) && is_numeric($this->args['width'])) 
                $width = intval($this->args['width']);
    
            foreach ($servers as $key => $server){
                if (substr($servers[$key]['img'], 0, 4) == "http"){
                    $servers[$key]['img_customlink'] = $servers[$key]['img'];
                }else if (substr($servers[$key]['img'], -4, 4) == ".png"){
                    $servers[$key]['img_customlink'] = "getimage/png/-/" . substr($servers[$key]['img'], 0, -4). "/". (string)(round((9 * $width)/16)) . str_replace(" ", "", " /$width/ ");
                }else if (substr($servers[$key]['img'], -4, 4) == ".jpg"){
                    $servers[$key]['img_customlink'] = "getimage/jpg/-/" . substr($servers[$key]['img'], 0, -4). "/". (string)(round((9 * $width)/16)) . str_replace(" ", "", " /$width/ ");
                }else if (substr($servers[$key]['img'], -4, 4) == "jpeg"){
                    $servers[$key]['img_customlink'] = "getimage/jpeg/-/" . substr($servers[$key]['img'], 0, -5). "/". (string)(round((9 * $width)/16)) . str_replace(" ", "", " /$width/ ");
                }
            }
            $cache = $this->formatedReturn($servers);
            $pvcache->write("serverStateAPI.dcms", $cache);
        }
        return $cache;
    }

}