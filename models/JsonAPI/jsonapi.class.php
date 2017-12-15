<?php
require_once('JSONAPI.php');

class Jsonapi_control {

  private $api = array();
  private $number_servers;
  private $server_id;
  private $config_file;
  private $mono_server = false;

  function __construct($serveur=false){
    $this->config_file = parse_ini_file(ROOT . "config/jsonapi.ini", true);
    if ($this->config_file['mono_server']){
      $this->mono_server = true;
      $this->server_id = $this->config_file['server_id'];
    }
    if ($serveur != false){
      $this->changeServer($serveur);
    }
    for ($i = 1; $i <= sizeof($this->config_file)-2; $i++){
      $this->api[$i] = new JSONAPI($this->config_file[$i]['host'], $this->config_file[$i]['port'], $this->config_file[$i]['username'], $this->config_file[$i]['password'], $this->config_file[$i]['salt']);
      $this->number_servers++;
    }
  }

  function getInfoOnServers($server=FALSE){
    if ($server != false && $server <= $this->number_servers){
        $maxJoueurs = $this->api[$server]->call("getPlayerLimit");
        if ($maxJoueurs[0]['success'] != 0){
          $nbJoueurs = $this->api[$server]->call("getPlayerCount");
          return array( "ServerName" => $this->getNameofServer($server), 
                        "ServerId" => $server,
                        "Description" => $this->config_file[$server]['desc'],
                        "MServer" => true, 
                        "MServerConfig" => $this->mono_server, 
                        "Connect" => true, 
                        "ImgDesc" => $this->config_file[$server]['img'],
                        "Slots" => $maxJoueurs[0]['success'], 
                        "Players_n" => $nbJoueurs[0]['success']);
        }else {
          return array( "ServerName" => $this->getNameofServer($server), 
                        "ServerId" => $server,
                        "Description" => $this->config_file[$server]['desc'], 
                        "MServer" => true, 
                        "MServerConfig" => $this->mono_server, 
                        "Connect" => false,
                        "ImgDesc" => $this->config_file[$server]['img'], 
                        "Slots" => null, 
                        "Players_n" => null);
        }
    }else if ($server != false){
      return false;
    }else if ($server == false){
      if ($this->mono_server){
        $maxJoueurs = $this->api[$server]->call("getPlayerLimit");
        if ($maxJoueurs['Success'] != 0){
          $nbJoueurs = $this->api[$server]->call("getPlayerCount");
          return array( "ServerName" => $this->getNameofServer($this->server_id), 
                        "ServerId" => $this->server_id,
                        "Description" => $this->config_file[$this->server_id]['desc'],
                        "MServer" => true, 
                        "MServerConfig" => $this->mono_server, 
                        "Connect" => true, 
                        "ImgDesc" => $this->config_file[$this->server_id]['img'],
                        "Slots" => $maxJoueurs['Success'], 
                        "Players_n" => $nbJoueurs['Success']);
        }else {
          return array( "ServerName" => $this->getNameofServer($this->server_id), 
                        "ServerId" => $this->server_id, 
                        "Description" => $this->config_file[$this->server_id]['desc'],
                        "MServer" => true, 
                        "MServerConfig" => $this->mono_server, 
                        "Connect" => false, 
                        "ImgDesc" => $this->config_file[$this->server_id]['img'],
                        "Slots" => null, 
                        "Players_n" => null);
        }
      }else {
        $status_servers = array();
        for ($i =1; $i <= $this->number_servers; $i++){
          $maxJoueurs = $this->api[$i]->call("getPlayerLimit");
          if ($maxJoueurs[0]['success'] != 0){
            $nbJoueurs = $this->api[$i]->call("getPlayerCount");
            array_push($status_servers, array( "ServerName" => $this->getNameofServer($i), 
                                                "ServerId" => $i,    
                                                "Description" => $this->config_file[$i]['desc'],
                                                "MServer" => true, 
                                                "MServerConfig" => $this->mono_server, 
                                                "Connect" => true,
                                                "ImgDesc" => $this->config_file[$i]['img'], 
                                                "Slots" => $maxJoueurs[0]['success'], 
                                                "Players_n" => $nbJoueurs[0]['success']));
          }else {
            array_push($status_servers, array( "ServerName" => $this->getNameofServer($i), 
                                                "ServerId" => $i,
                                                "Description" => $this->config_file[$i]['desc'],
                                                "MServer" => true, 
                                                "MServerConfig" => $this->mono_server, 
                                                "Connect" => false,
                                                "ImgDesc" => $this->config_file[$i]['img'], 
                                                "Slots" => null, 
                                                "Players_n" => null));
          }
        }
        return $status_servers;
      }
    }else {
      return false;
    }
  }

  function getBanList(){
    if ($this->mono_server){
      $banlist = $this->api[$this->server_id]->call("players.banned.names");
    }else {
      $banlist = array();
      for ($i =1; $i <= $this->number_servers; $i++){
        $Inf = $this->getInfoOnServers($i);
        if ($Inf != false && $Inf["Connect"]){
          array_push($banlist, $this->api[$i]->call("players.banned.names"));
        }else {
          array_push($banlist, false);
        }
      }
    }
    return $banlist;
  }

  function getPlayersOnline(){
    if ($this->mono_server){
      $banlist = $this->api[$this->server_id]->call("players.offline");
    }else {
      $banlist = array();
      for ($i =1; $i <= $this->number_servers; $i++){
        $Inf = $this->getInfoOnServers($i);
        if ($Inf != false && $Inf["Connect"]){
          array_push($banlist, $this->api[$i]->call("players.offline"));
        }else {
          array_push($banlist, false);
        }
      }
    }
    return $banlist;
  }
  

  function getServeursConnect(){
    $connect = array();
    if ($this->mono_server){
      array_push($connect, $this->api[$this->server_id]->call("players.online.names"));
      array_push($connect, $this->api[$this->server_id]->call("players.offline.names"));
    }else {
      for ($i =1; $i <= $this->number_servers; $i++){
        $Inf = $this->getInfoOnServers($i);
        if ($Inf != false && $Inf["Connect"]){
          array_push($connect, $this->api[$i]->call("players.online.names"));
          array_push($connect, $this->api[$i]->call("players.offline.names"));
        }else {
          array_push($connect, false);
        }
      }
    }
    return $connect;
  }

  function getNumberServers(){
    if ($this->mono_server){
      return 1;
    }
    return $this->number_servers;
  }

  function getNameofServer($id_server){
    if ($id_server <= $this->number_servers){
      return $this->config_file[$id_server]['name'];
    }else {
      return false;
    }
  }

  function changeServer($s_id){
    if ($s_id <= $this->number_servers){
      $this->server_id = $s_id;
      $this->mono_server = true;
      //Ecriture dans le fichier ini
        //Copie du fichier dans un array temporaire
        $temp_conf = $this->config_file;
        //On modifie l'array temporaire
        $temp_conf['mono_server'] = true;
        $temp_conf['server_id'] = $s_id;
        //On appel la class ini pour réecrire le fichier
        $ini = new ini (ROOT . "config/jsonapi.ini", 'Configuration JSONAPI');
        //On lui passe l'array modifié
        $ini->ajouter_array($temp_conf);
        //On écrit en lui demmandant de conserver les groupes
        $ini->ecrire(true);
      //FIN Encriture ini
      return true;
    }else {
      return false;
    }
  }
  
}