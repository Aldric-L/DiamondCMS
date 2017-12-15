<?php
  //On charge les class qui permetteront de charger les fichier YAML
  //Les fichiers config sont en YAML (YML)
  /*require(ROOT . 'models/config_YAML/class_yaml.php');
  $reader = new Load('models/config_YAML/files/config.yml');
  $Serveur_Config = $reader->GetContentYml();*/
  $Serveur_Config = parse_ini_file(ROOT . "config/config.ini", true);
  //print_r($ini_array);
  //var_dump($ini_array['Global']);

?>
