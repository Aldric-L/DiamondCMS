<?php 

//Si on passe en mode XHR
if (isset($param[2]) && !empty($param[2]) && $param[2] == "delete" && isset($param[3])  && isset($param[4])){
    $i = 0;
    if ($dir = opendir(ROOT . 'views/uploads/img/')) {
        while($file = readdir($dir)) {
            //On n'ouvre surtout pas les sous-dossiers
            if(!is_dir(ROOT . 'views/uploads/img/' . $file) && !in_array($file, array(".",".."))) {
                //Si l'id du fichier, et sa taille correspondent, on est quasiment totalement sûrs que c'est le bon fichier
                if ($i == $param[3] && $param[4] == filesize(ROOT . 'views/uploads/img/' . $file)){
                    //On le supprime
                    if (unlink(ROOT . 'views/uploads/img/' . $file) == true){
                        die('Success');
                    }else {
                        $controleur_def->addError(540);
                        die('Error');
                    }
                }
            $i = $i+1;
            }
        }
      closedir($dir);
    }
      $controleur_def->addError(541);
      die('Error');
}

//Si une image est uploadée
if (isset($_FILES['img']) && $_FILES['img']['size'] != 0){
    //Si le fichier est bien une image
    if (strrpos($_FILES['img']['type'], "image/") === false){
        $controleur_def->addError(524);
    }else {
        $upload = uploadFile('img', null, false);
        if (is_int($upload)){
            $controleur_def->addError(500 + intval($upload));
        }
    }   
}

$images = array();

//Chargement des images "protégées", c'est-à-dire essentielles au fonctionnement du CMS
$locked_img = file_get_contents(ROOT . 'views/uploads/img/locked_files.dfiles');
$locked_img = explode(';', $locked_img);
//Chargement de la liste des images
$i = 0;
if ($dir = opendir(ROOT . 'views/uploads/img/')) {
    while($file = readdir($dir)) {
      //On n'ouvre surtout pas les sous-dossiers
      if(!is_dir(ROOT . 'views/uploads/img/' . $file) && !in_array($file, array(".","..")) && $file != "locked_files.dfiles") {
        if (in_array($file, $locked_img)){
            array_push($images, 
            array($i, 
                    $file, 
                    ROOT . 'views/uploads/img/' . $file, 
                    filemtime(ROOT . 'views/uploads/img/' . $file), 
                    FileSizeConvert( filesize(ROOT . 'views/uploads/img/' . $file) ),
                    filesize(ROOT . 'views/uploads/img/' . $file),
                    true ) );
        }else {
            array_push($images, 
            array($i, 
                    $file, 
                    ROOT . 'views/uploads/img/' . $file, 
                    filemtime(ROOT . 'views/uploads/img/' . $file), 
                    FileSizeConvert( filesize(ROOT . 'views/uploads/img/' . $file) ),
                    filesize(ROOT . 'views/uploads/img/' . $file),
                    false ) );
        }
        $i = $i+1;
      }
    }
    closedir($dir);
  }
$controleur_def->loadJS('admin/images');
$controleur_def->loadViewAdmin('admin/images', 'accueil', 'Gestionnaire des images');