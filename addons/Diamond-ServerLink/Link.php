<?php 
/**
 * Cette class permet d'initier toutes les connexions query : il s'agit de la class "suprème" puisquelle dirige toutes les autres (spécifiques à un jeu)
 *
 * @author Aldric L.
 * @copyright 2020
 */

namespace DServerLink;

class Query {
    private $controleur_def;
    private $config_file;
    private $private_errors = false;
    private $errors = array();
    private $isloaded = false;

    // Attention, ce tableau commence à 1
    // Il est de la structure suivant : array( instance classe, array(host, port, timeout, nom du jeu, id, -une fois que la méthode connect a été appelée- success))
    private $api = array();

    //Tableau dans lequel on stock tous les IDENTIFIANTs et non les instances des serveurs dont on a initié, avec succès, la connexion
    private $connectedServers = array();

    /**
    * Constructeur : contrairement aux autres class, ce constructeur n'instancie aucune connexion, il charge juste la config.
    *
    * @param $controleur_def : class controleur de DiamondCMS dont on garde une instance
    * @param $cm : class ConfigManager de l'addon dont on garde une instance
    * @param boolean $private_errors : si true, on n'utilise pas le controleur def pour lever des erreurs
    * @author Aldric L.
    * @copyright 2020
    */
    public function __construct($controleur_def, $cm, $private_errors=false){
        $this->controleur_def = $controleur_def;
        $this->config_file = $cm->getConfig();
        if ($private_errors){
            $this->private_errors = true;
        }
    }

    /**
    * newError : cette fonction permet de lever une erreur.
    *
    * @author Aldric L.
    * @copyright 2020
    * @return array|boolean
    */
    private function newError($error_code){
        if ($this->private_errors){
            array_push($this->errors, $error_code);
            return;
        }else {
            $this->controleur_def->addError($error_code);
        }
    }

    /**
    * getErrors : cette fonction permet de récupérer les erreurs stockées si private_errors est activé.
    *
    * @author Aldric L.
    * @copyright 2020
    * @return array|boolean
    */
    public function getErrors(){
        if ($this->private_errors){
            return $this->errors;
        }
        return false;
    }

    /**
    * load : cette fonction permet d'instancier les class de connexion aux serveurs dans un tableau pour les utiliser plus tard.
    *
    * @author Aldric L.
    * @copyright 2020
    * @return void
    */
    private function load(){
        if ($this->isloaded){
            return;
        }
        for ($i = 1; $i <= sizeof($this->config_file); $i++){
            if (($this->config_file[$i]["game"] == "Minecraft-Java" || $this->config_file[$i]["game"] == "Minecraft-MPCE") && ($this->config_file[$i]["enabled"] == "true")){
                $this->api[$i] = array(new MinecraftQuery\MinecraftQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["queryport"], 1, $this->config_file[$i]["game"], $this->config_file[$i]["id"], false));
            }else if (($this->config_file[$i]["game"] == "GMod" || $this->config_file[$i]["game"] == "Garry's Mod") && ($this->config_file[$i]["enabled"] == "true")){
                $this->api[$i] = array(new SourceQuery\SourceQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["queryport"], 5, $this->config_file[$i]["game"], $this->config_file[$i]["id"], false));
            }
        }
        $this->isloaded = true;
    }

    /**
    * connect : cette fonction permet de se connecter à chaque serveur précedemment instancié
    *
    * @param int $server : id du serveur auquel il faut SEULEMENT se connecter => peut être nul
    * @author Aldric L.
    * @copyright 2020
    * @return void
    */
    public function connect($server=NULL){
        if (!$this->isloaded){
            $this->load();
        }
        for ($i = 1; $i <= sizeof($this->config_file); $i++){
            if ($server == null || ($server != null && isset($this->api[$i]) && $server == $this->api[$i][1][4])){
                try
                {
                    if (isset($this->api[$i])){
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->api[$i][1][5] = true;                            
                            $this->api[$i][0]->Connect( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2]);
                        }else if ($this->api[$i][1][3] == "Minecraft-MPCE"){
                            $this->api[$i][1][5] = true;                            
                            $this->api[$i][0]->ConnectBedrock( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2]);
                        }else if ($this->api[$i][1][3] == "GMod" || $this->api[$i][1][3] == "Garry's Mod" || $this->api[$i][1][3] == "Team-Fortress 2" || $this->api[$i][1][3] == "CS-GO"){
                            $this->api[$i][1][5] = true;                            
                            $this->api[$i][0]->Connect( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2], SourceQuery\SourceQuery::SOURCE);
                        }
                    }
                }
                catch( \Exception  $e )
                {
                    if ($e->getMessage() != 'Failed to receive challenge.'){
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->newError(400);
                        }else if ($this->api[$i][1][3] == "Minecraft-MPCE"){
                            $this->newError("400b");
                        }else {
                            $this->newError("400c");
                        }
                        $this->api[$i][1][5] = false;
                    }
                }
                finally {
                    //var_dump($this->api, $i);
                    if (isset($this->api[$i]) && $this->api[$i][1][5] == true){
                        //var_dump($this->api);
                        array_push($this->connectedServers, $this->api[$i][1][4]);
                    }
                }
            }   
        }

    }

    /**
    * getInfos : cette fonction permet de récuperer les informations des serveurs instanciés
    *
    * @param int $server : id du serveur auquel il faut SEULEMENT se connecter => peut être nul
    * @author Aldric L.
    * @copyright 2020
    * @return array
    */
    public function getInfos($server=NULL){
        $inf = array();
        for ($i = 1; $i <= sizeof($this->config_file); $i++){
            if (($server == null && isset($this->api[$i])) || (isset($this->api[$i]) && intval($this->api[$i][1][4]) == $server)){
                try {
                    try {
                        $inf[$i]['results'] = $this->api[$i][0]->GetInfo();
                    }catch ( \Exception $e ) {
                        $inf[$i]['results'] = false;
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->newError(410);
                        }else if ($this->api[$i][1][3] == "Minecraft-MPCE"){
                            $this->newError("410b");
                        }else {
                            $this->newError("410c");
                        }
                    }
                }catch (\Exception $ex){
                    $inf[$i]['results'] = false;
                    if ($this->api[$i][1][3] == "Minecraft-Java"){
                        $this->newError(440);
                    }else if ($this->api[$i][1][3] == "Minecraft-MPCE"){
                        $this->newError("440b");
                    }else {
                        $this->newError("440c");
                    }
                }
                
                foreach ($this->config_file[$i] as $k => $conf){
                    $inf[$i][$k] = $conf;
                }
            }else if ($server == null && !isset($this->api[$i])){
                $inf[$i]['results'] = false;
                foreach ($this->config_file[$i] as $k => $conf){
                    $inf[$i][$k] = $conf;
                }
            }
        }
        //var_dump($inf);
        return $inf;
    }

    /**
    * getPlayers : cette fonction permet de récuperer les informations des serveurs instanciés
    *
    * @param int $server : id du serveur auquel il faut SEULEMENT se connecter => peut être nul
    * @author Aldric L.
    * @copyright 2020
    * @return array
    */
    public function getPlayers($server=NULL){
        $inf = array();
        for ($i = 1; $i <= sizeof($this->config_file); $i++){
            if (($server == null && isset($this->api[$i])) || (isset($this->api[$i]) && intval($this->api[$i][1][4]) == $server)){
                try {
                    try {
                        $inf[$i]['results'] = $this->api[$i][0]->GetPlayers();
                    }catch ( \Exception $e ) {
                        $inf[$i]['results'] = false;
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->newError(420);
                        }else if ($this->api[$i][1][3] == "Minecraft-MPCE"){
                            $this->newError("420b");
                        }else {
                            $this->newError("420c");
                        }
                    }
                }catch (Exception $ex){
                    $inf[$i]['results'] = false;
                    if ($this->api[$i][1][3] == "Minecraft-Java"){
                        $this->newError(440);
                    }else if ($this->api[$i][1][3] == "Minecraft-MPCE"){
                        $this->newError("440b");
                    }else {
                        $this->newError("440c");
                    }
                }
                
                foreach ($this->config_file[$i] as $k => $conf){
                    $inf[$i][$k] = $conf;
                }
            }else if ($server == null && !isset($this->api[$i])){
                $inf[$i]['results'] = false;
                foreach ($this->config_file[$i] as $k => $conf){
                    $inf[$i][$k] = $conf;
                }
            }
        }
        return $inf;
    }

    /**
    * getNbServersLoaded : cette fonction permet de récuperer le nombre de serveurs loadés donc activés
    *
    * @author Aldric L.
    * @copyright 2020
    * @return int
    */
    public function getNbServersLoaded(){
        return sizeof($this->api);
    }

    /**
    * getNbServers : cette fonction permet de récuperer le nombre TOTAL de serveurs indiqués dans la config (même ceux qui ne sont pas connectés ou qui sont désactivés)
    *
    * @author Aldric L.
    * @copyright 2020
    * @return int
    */
    public function getNbServers(){
        if (!$this->isloaded){
            $this->load();
        }
        return sizeof($this->config_file);
    }

    /**
    * getGame : cette fonction permet de récuperer les jeux des servers
    *
    * @param int $server représente l'id du serveur dont on veut connaitre le jeu
    * @author Aldric L.
    * @copyright 2020
    * @return array|string
    */
    public function getGame($server=NULL){
        if (!$this->isloaded){
            $this->load();
        }
        if ($server != null) {
            if (isset($this->config_file[intVal($server)])){
                return $this->config_file[intVal($server)]["game"];
            }
        }else {
            $games = array();
            foreach ($this->config_file as $c){
                $games[$c['id']] = $c['game'];
            }
            return $games;
        }
    }

    /**
    * disconnect : cette fonction permet de se deconnecter proprement de chaque serveur précedemment connecté
    *
    * @param int $server : id du serveur auquel il faut SEULEMENT se deconnecter => peut être nul
    * @author Aldric L.
    * @copyright 2020
    * @return void
    */
    public function disconnect($server=NULL){
        for ($i = 1; $i <= sizeof($this->config_file); $i++){
            if (($server == null || ($server != null && isset($this->api[$i]) && $server == $this->api[$i][1][4])) ){
                try
                {
                    if (isset($this->api[$i])){
                        if ($this->api[$i][1][5]){ 
                            if ($this->api[$i][1][3] == "Minecraft-MPCE" || $this->api[$i][1][3] == "Minecraft-Java"){
                                // Visiblement xPaw n'a pas implémenté cette méthode alors qu'elle figure dans la doc.. à surveiller
                                //$this->api[$i][0]->close();
                            }else {
                                $this->api[$i][0]->Disconnect();
                            }        
                        }
                    }
                }
                catch( \Exception  $e )
                {
                    if ($e->getMessage() != 'Failed to receive challenge.'){
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->newError(420);
                        }else if ($this->api[$i][1][3] == "Minecraft-MPCE"){
                            $this->newError("420b");
                        }else {
                            $this->newError("420c");
                        }
                    }
                }
                finally {
                    if (isset($this->api[$i]) /*&& $this->api[$i][1][5] == true*/){
                        //var_dump($this->api);
                        unset($this->api[$i]);
                    }
                }
            }   
        }
        $this->isloaded = false;
        $this->api = array();
    }

}

/**
 * Cette class permet d'initier toutes les connexions rcon : il s'agit de la class "suprème" puisquelle dirige toutes les autres (spécifiques à un jeu)
 *
 * Attention, contrairement à Query, cette class ne supporte pas la version MCPE de Minecraft
 * @author Aldric L.
 * @copyright 2020
 */
 
 class RCon {
     private $controleur_def;
     private $config_file;
     private $isloaded = false;
     private $private_errors = false;
     private $errors = array();
 
     // Attention, ce tableau commence à 1
     // Il est de la structure suivant : array( instance classe, array(host, port, timeout, nom du jeu, id, -une fois que la méthode connect a été appelée- success))
     private $api = array();
 
     //Tableau dans lequel on stock tous les IDENTIFIANTs et non les instances des serveurs dont on a initié, avec succès, la connexion
     private $connected_servers = array();
 
     /**
     * Constructeur : contrairement aux autres class, ce constructeur n'instancie aucune connexion, il charge juste la config.
     *
     * @param $controleur_def : class controleur de DiamondCMS dont on garde une instance
     * @param $cm : class ConfigManager de l'addon dont on garde une instance
     * @author Aldric L.
     * @copyright 2020
     */
     public function __construct($controleur_def, $cm, $private_errors=false){
         $this->controleur_def = $controleur_def;
         $this->config_file = $cm->getConfig();
         if ($private_errors){
             $this->private_errors = true;
         }
     }

     /**
    * newError : cette fonction permet de lever une erreur.
    *
    * @author Aldric L.
    * @copyright 2020
    * @return array|boolean
    */
    private function newError($error_code){
        if ($this->private_errors){
            array_push($this->errors, $error_code);
            return;
        }else {
            $this->controleur_def->addError($error_code);
        }
    }

    /**
    * getErrors : cette fonction permet de récupérer les erreurs stockées si private_errors est activé.
    *
    * @author Aldric L.
    * @copyright 2020
    * @return array|boolean
    */
    public function getErrors(){
        if ($this->private_errors){
            return $this->errors;
        }
        return false;
    }
 
     /**
     * load : cette fonction permet d'instancier les class de connexion aux serveurs dans un tableau pour les utiliser plus tard.
     *
     * @author Aldric L.
     * @copyright 2020
     * @return void
     */
     private function load(){
         for ($i = 1; $i <= sizeof($this->config_file); $i++){
             if (($this->config_file[$i]["game"] == "Minecraft-Java") && ($this->config_file[$i]["enabled"] == "true")){
                 $this->api[$i] = array(new SourceQuery\SourceQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["port"], 1, $this->config_file[$i]["game"], $this->config_file[$i]["id"], false, $this->config_file[$i]["password"]));
             }else if (($this->config_file[$i]["game"] == "GMod" || $this->config_file[$i]["game"] == "Garry's Mod") && ($this->config_file[$i]["enabled"] == "true")){
                 $this->api[$i] = array(new SourceQuery\SourceQuery(), array($this->config_file[$i]["host"], $this->config_file[$i]["port"], 5, $this->config_file[$i]["game"], $this->config_file[$i]["id"], false, $this->config_file[$i]["password"]));
             }
         }
     }
 
     /**
     * connect : cette fonction permet de se connecter à chaque serveur précedemment instancié
     *
     * @param int $server : id du serveur auquel il faut SEULEMENT se connecter => peut être nul
     * @author Aldric L.
     * @copyright 2020
     * @return void
     */
     public function connect($server=NULL){
         if (!$this->isloaded){
             $this->load();
         }
         for ($i = 1; $i <= sizeof($this->config_file); $i++){
             if ($server == null || ($server != null && isset($this->api[$i]) && $server == $this->api[$i][1][4])){
                 try
                 {
                     if (isset($this->api[$i])){
                         if ($this->api[$i][1][3] == "Minecraft-Java"){
                             $this->api[$i][1][5] = true;                            
                             $this->api[$i][0]->Connect( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2]);
                             $this->api[$i][0]->SetRconPassword($this->api[$i][1][6]);                             
                         }else if ($this->api[$i][1][3] == "GMod" || $this->api[$i][1][3] == "Garry's Mod" || $this->api[$i][1][3] == "Team-Fortress 2" || $this->api[$i][1][3] == "CS-GO"){
                             $this->api[$i][1][5] = true;                            
                             $this->api[$i][0]->Connect( $this->api[$i][1][0], $this->api[$i][1][1], $this->api[$i][1][2], SourceQuery\SourceQuery::SOURCE);
                             $this->api[$i][0]->SetRconPassword($this->api[$i][1][6]);                             
                         }
                     }
                 }
                 catch( \Exception  $e )
                 {
                     if ($e->getMessage() != 'Failed to receive challenge.'){
                         if ($this->api[$i][1][3] == "Minecraft-Java"){
                             $this->newError(450);
                         }else {
                             $this->newError("450b");
                         }
                         $this->api[$i][1][5] = false;
                     }
                 }
                 finally {
                     //var_dump($this->api, $i);
                     if (isset($this->api[$i]) && $this->api[$i][1][5] == true){
                         //var_dump($this->api);
                         array_push($this->connected_servers,intval($this->api[$i][1][4]));
                     }
                 }
             }   
         }
     }

    /**
     * execOnServer : cette fonction permet d'éxecuter une commande sur un serveur
     *
     * @param int $id : id du serveur auquel il faut envoyer la commande
     * @param string $cmd : commande qu'il faut exécuter
     * @author Aldric L.
     * @copyright 2020
     * @return bool
     */
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
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->controleur_def->newError(470);
                        }else {
                            $this->controleur_def->newError("470b");
                        }
                    }
                    return $return;
                }
            }
        }
    }

    /**
     * disconnect : cette fonction permet de se deconnecter proprement de chaque serveur précedemment connecté
     *
     * @param int $server : id du serveur auquel il faut SEULEMENT se deconnecter => peut être nul
     * @author Aldric L.
     * @copyright 2020
     * @return void
     */
    function disconnect($server=null){
        for ($i = 1; $i <= sizeof($this->api); $i++){
            if (isset($this->api[$i]) && in_array(intval($this->api[$i][1][4]), $this->connected_servers)){
                if ($server == null || (isset($this->api[$i]) && $server == $this->api[$i][1][4])){
                    try
                    {
                        $this->api[$i][0]->Disconnect();
                    }
                    catch( Exception $e )
                    {
                        if ($this->api[$i][1][3] == "Minecraft-Java"){
                            $this->controleur_def->newError(460);
                            $pb = $e;
                        }else {
                            $this->controleur_def->newError("460b");
                            $pb = $e;
                        }
                    }
                }  
            }
        }
    }
 
     /**
     * getNbServersLoaded : cette fonction permet de récuperer le nombre de serveurs loadés donc activés
     *
     * @author Aldric L.
     * @copyright 2020
     * @return int
     */
     public function getNbServersLoaded(){
         return sizeof($this->api);
     }
 
     /**
     * getNbServers : cette fonction permet de récuperer le nombre TOTAL de serveurs indiqués dans la config (même ceux qui ne sont pas connectés ou qui sont désactivés)
     *
     * @author Aldric L.
     * @copyright 2020
     * @return int
     */
     public function getNbServers(){
         if (!$this->isloaded){
             $this->load();
         }
         return sizeof($this->config_file);
     }
 
     /**
     * getGame : cette fonction permet de récuperer les jeux des servers
     *
     * @param int $server représente l'id du serveur dont on veut connaitre le jeu
     * @author Aldric L.
     * @copyright 2020
     * @return array|string
     */
     public function getGame($server=NULL){
         if (!$this->isloaded){
             $this->load();
         }
         if ($server != null) {
             if (isset($this->config_file[intVal($server)])){
                 return $this->config_file[intVal($server)]["game"];
             }
         }else {
             $games = array();
             foreach ($this->config_file as $c){
                 $games[$c['id']] = $c['game'];
             }
             return $games;
         }
     }
    
 
 }