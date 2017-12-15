<?php
$banlist = $jsonapi->getBanList();
if (empty($banlist)){
  $empty = true;
}else {
  $server_status = array();
  $server_null = 0;
  for ($i=0; $i<sizeof($banlist); $i++){
    if ($banlist[$i] == false){
      array_push($server_status, array('IdServer' => $i, "ServerName" => $jsonapi->getNameofServer($i+1), "Connect" => false));
      $server_null++;
    }else {
      array_push($server_status, array('IdServer' => $i, "ServerName" => $jsonapi->getNameofServer($i+1), "Connect" => true));
    }
  }
  if ($server_null == sizeof($banlist)){
    $empty = true;
  }
}
$controleur_def->loadView('pages/banlist', 'emptyServer', 'BanList');
