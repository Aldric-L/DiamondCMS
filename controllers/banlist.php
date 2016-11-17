<?php
$banlist = $jsonapi->getBanList();
if (!isset($banlist) || empty($banlist)){
  $empty = true;
}
$controleur_def->loadView('pages/banlist', 'emptyServer', 'BanList');
