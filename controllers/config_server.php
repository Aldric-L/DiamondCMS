<?php
  //On charge les class qui permetteront de charger les fichier YAML
  //Les fichiers config sont en YAML (YML)
  require(ROOT . 'models/config_YAML/class_yaml.php');
  $reader = new Load('models/config_YAML/files/config.yml');
  $Serveur_Config = $reader->GetContentYml();

    /*$installLecture = new Load('file/config.yml');
    $installLecture = $installLecture->GetContentYml();
    $installLecture['is_install'] = true;
    $writer = new Write('models/config_YAML/file/config.yml', $installLecture);*/

?>
