<?php
require_once('JSONAPI.php');

class Jsonapi_control {

  private $api;

  function __construct($serveur){
    $config_json = new Load('models/config_YAML/files/jsonapi.yml');
    $config_file = $config_json->GetContentYml();
    $this->api = new JSONAPI($config_file[$serveur]['host'], $config_file[$serveur]['port'], $config_file[$serveur]['username'], $config_file[$serveur]['password'], $config_file[$serveur]['salt']);
  }

  function getBanList(){
    $banlist = $this->api->call("players.banned.names");
    return $banlist;
  }

}
