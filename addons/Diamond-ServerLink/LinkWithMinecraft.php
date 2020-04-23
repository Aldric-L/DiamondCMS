<?php
namespace DServerLink;
/**
 * Class permettant de faire le lien (Ici d'utiliser la fonction query) entre un serveur Minecraft et DiamondCMS
 * Construit sur le projet "Minecraft Query PHP" et "Source Query" de xpaw
 * 
 * @deprecated 
 * @author Aldric L.
 * @copyright 2020
 */

class QueryWithMinecraft {
    private $controleur_def;
    private $api = array();
    private $config_file;
    private $needed_serveurs = array();
  
    //Le controleur initie la connexion avec soit
    // - tous les serveurs minecraft dans le fichier serveurs.ini
    // - les serveurs dont l'id est précisé dans l'array $needed_servers
    public function __construct($controleur_def, $cm, $needed_servers = array()){
        $this->controleur_def = $controleur_def;
        $this->config_file = $cm->getConfig();
        if (isset($needed_servers) && !empty($needed_servers)){
            $this->needed_servers = $needed_servers;
            for ($i = 1; $i <= sizeof($this->config_file); $i++){
                if (!empty(array_keys($this->needed_servers, $this->config_file[$i]["id"])) && ($this->config_file[$i]["game"] == "Minecraft-Java" || $this->config_file[$i]["game"] == "Minecraft-MPCE") && ($this->config_file[$i]["enabled"] == "true")){
                    $this->api[$i] = array(new MinecraftQuery\MinecraftQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["queryport"], 1, $this->config_file[$i]["game"], $this->config_file[$i]["id"]));
                }
            }
        }else {
            for ($i = 1; $i <= sizeof($this->config_file); $i++){
                if (($this->config_file[$i]["game"] == "Minecraft-Java" || $this->config_file[$i]["game"] == "Minecraft-MPCE") && ($this->config_file[$i]["enabled"] == "true")){
                    $this->api[$i] = array(new MinecraftQuery\MinecraftQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["queryport"], 1, $this->config_file[$i]["game"], $this->config_file[$i]["id"]));
                }
            }
        }
    }

    function connect(){
        for ($i = 1; $i <= sizeof($this->api); $i++){
            try
            {
                if ($this->api[$i][1][3] == "Minecraft-Java"){
                    $this->api[$i][0]->Connect( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2]);
                }else if ($this->api[i][1][3] == "Minecraft-MPCE"){
                    $this->api[$i][0]->ConnectBedrock( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2]);
                }
            }
            catch( MinecraftQuery\MinecraftQueryException $e )
            {
                if ($e->getMessage() != 'Failed to receive challenge.'){
                    $this->controleur_def->addError(400);
                }
            }
        }
    }

    function getInfoOnServers(){
        $infos_serveurs = array();
        for ($i = 1; $i <= sizeof($this->api); $i++){
            $inf = array();
            $inf['results'] = $this->api[$i][0]->GetInfo();
            foreach ($this->config_file[$i] as $k => $conf){
                $inf[$k] = $conf;
            }
            array_push($infos_serveurs, $inf);
        }
        return $infos_serveurs;
    }

    function getInfoOnServer($id){
        for ($i = 1; $i <= sizeof($this->api); $i++){
            if (intval($this->api[$i][1][4]) == $id){
                $inf = array();
                $inf['results'] = $this->api[$i][0]->GetInfo();
                foreach ($this->config_file[$i] as $k => $conf){
                    $inf[$k] = $conf;
                }
                return $inf;
            }
        }
    }

    function getPlayers($serverid){
        for ($i = 1; $i <= sizeof($this->api); $i++){
            if (intval($this->api[$i][1][4]) == $serverid){
                $inf = array();
                $inf['results'] = $this->api[$i][0]->GetPlayers();
                return $inf;
            }
        }
    }

    function getNbServers(){
        return sizeof($this->api);
    }

}

/**
 * Class permettant de faire le lien (Ici d'utiliser la fonction rcon) entre un serveur Minecraft et DiamondCMS
 * Construit sur le projet "Minecraft Query PHP" et "Source Query" de xpaw
 * 
 * Attention, contrairement à QueryWithMinecraft, cette class ne supporte pas la version MCPE de Minecraft
 * @author Aldric L.
 * @copyright 2020
 */

 class RConWithMinecraft {
    private $controleur_def;
    private $api = array();
    private $config_file;
    private $needed_serveurs = array();
    private $connected_servers = array();
  
    //Le controleur initie la connexion avec soit
    // - tous les serveurs minecraft dans le fichier serveurs.ini
    // - les serveurs dont l'id est précisé dans l'array $needed_servers
    public function __construct($controleur_def, $cm, $needed_servers = array()){
        $this->controleur_def = $controleur_def;
        //$this->config_file = parse_ini_file(ROOT . "config/serveurs.ini", true);
        $this->config_file = $cm->getConfig();

        if (isset($needed_servers) && !empty($needed_servers)){
            $this->needed_servers = $needed_servers;
            for ($i = 1; $i <= sizeof($this->config_file); $i++){
                if (!empty(array_keys($this->needed_servers, $this->config_file[$i]["id"])) && ($this->config_file[$i]["game"] == "Minecraft-Java") && ($this->config_file[$i]["enabled"] == "true")){
                    $this->api[$i] = array(new SourceQuery\SourceQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["port"], 1, $this->config_file[$i]["game"], $this->config_file[$i]["id"], $this->config_file[$i]["password"]));
                }
            }
        }else {
            for ($i = 1; $i <= sizeof($this->config_file)-2; $i++){
                if ($this->config_file[$i]["game"] == "Minecraft-Java" && ($this->config_file[$i]["enabled"] == "true")){
                    $this->api[$i] = array(new SourceQuery\SourceQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["port"], 1, $this->config_file[$i]["game"], $this->config_file[$i]["id"], $this->config_file[$i]["password"]));
                }
            }
        }
    }

    function connect(){
        for ($i = 1; $i <= sizeof($this->api); $i++){
            if (!in_array(intval($this->api[$i][1][4]), $this->connected_servers)){
                try
                {
                    if ($this->api[$i][1][3] == "Minecraft-Java"){
                        array_push($this->connected_servers, intval($this->api[$i][1][4]));
                        $this->api[$i][0]->Connect( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2], SourceQuery\SourceQuery::SOURCE);
                        $this->api[$i][0]->SetRconPassword($this->api[$i][1][5]);
                    }
                }
                catch( Exception  $e )
                {
                    if ($e->getMessage() != 'Failed to receive challenge.'){
                        //$this->controleur_def->addError(400);
                    }
                    array_pop($this->connected_servers);
                }
            }
        }
    }

    function connectOneServer($id){
        if (!in_array($id, $this->connected_servers)){
            for ($i = 1; $i <= sizeof($this->api); $i++){
                if (intval($this->api[$i][1][4]) == $id){
                    try
                    {
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->api[$i][0]->Connect( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2], SourceQuery\SourceQuery::SOURCE);
                            $this->api[$i][0]->SetRconPassword($this->api[$i][1][5]);
                            array_push($this->connected_servers, $id);
                        }
                    }
                    catch( Exception  $e )
                    {
                        if ($e->getMessage() != 'Failed to receive challenge.'){
                            //$this->controleur_def->addError(400);
                        }
                        array_pop($this->connected_servers);
                    }
                }
            }
        }  
    }

    function getNbServers(){
        return sizeof($this->api);
    }

    function execOnServer($id, $cmd){
        if (in_array($id, $this->connected_servers)){
            for ($i = 1; $i <= sizeof($this->api); $i++){
                if (intval($this->api[$i][1][4]) == $id){
                    try
                    {
                        $return = $this->api[$i][0]->Rcon($cmd);
                    }
                    catch( Exception $e )
                    {
                        $this->controleur_def->addError(401);
                    }
                    return $return;
                }
            }
        }
    }

    function disconnect(){
        for ($i = 1; $i <= sizeof($this->api); $i++){
            if (in_array(intval($this->api[$i][1][4]), $this->connected_servers)){
                try
                {
                    $this->api[$i][0]->Disconnect();
                }
                catch( Exception $e )
                {
                    $this->controleur_def->addError(402);
                }
            }
        }
    }
    

}