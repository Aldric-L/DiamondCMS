<?php

if ($param[1] == "seen" && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
  $controleur_def->purgeErrors();
  //var_dump($controleur_def->getErrors());
  if ($controleur_def->getErrors() != null){
    exit ("Error !");
  }
}
