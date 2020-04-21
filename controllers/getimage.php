<?php 
/**
 * Ce controleur permet de charger une image à la dimension voulue (il rogne l'image au besoin)
 * Attention, il nécessite phpGD pour fonctionner
 *
 * Le code est principalement issu de mon-beulogne.com (https://mon-beulogue.com/2007/06/11/redimensionner-et-rogner-une-image-en-php/)
 * Le modèle est donc sous licence libre, et n'appartient pas à Aldric L. qui l'a simplement adapté au projet DiamondCMS
 * Le controlleur, lui, est principalement le fruit du travail d'Aldric L. (@author)
 * 
 * Sous l'angle technique,
 * $param[1] est l'extension du fichier (png ou jpg)
 * $param[2] est le sous dossier de l'image dans views/uploads/img/ (si il n'est pas nécessaire, écrire "-")
 * $param[3] est le nom de l'image
 * $param[4] est la hauteur de l'image
 * $param[5] est la largeur de l'image
 */

 //Si tous les paramètres sont bien spécifiés
if (isset($param[1]) && !empty($param[1]) && 
    isset($param[2]) && !empty($param[2]) &&
    isset($param[3]) && !empty($param[3]) && 
    isset($param[4]) && !empty($param[4]) && 
    isset($param[5]) && !empty($param[5]) ){
    
    //si le fichier existe à l'emplacement indiqué
    if (($param[2] == '-' && file_exists(ROOT . 'views/uploads/img/' . $param[3] . '.' . $param[1])) || file_exists(ROOT . 'views/uploads/img/' . $param[2] . '/' . $param[3] . '.' . $param[1])){
        if ($param[1] == "png"){
            //on génère le chemin d'accès
            if ($param[2] == '-'){
                $folder = ROOT . 'views/uploads/img/' . $param[3] . '.' . $param[1];
            }else {
                $folder = ROOT . 'views/uploads/img/' . $param[2] . '/' . $param[3] . '.' . $param[1];
            }
            if (is_numeric($param[4]) && is_numeric($param[5])){
                $height = $param[4];
                $width = $param[5];
            }else {
                $controleur_def->addError(130);
                die("Erreur de paramètres (4)");
            }
            //On charge le model
            $controleur_def->loadModel('getimage/getimage_png');

            header('Pragma: no-cache');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Expires: Mon, 1 Jan 2020 05:00:00 GMT');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private',false);
            header('Content-type: image/png');
             
            imagepng(resize($folder, $width, $height));
        }else if ($param[1] == "jpg"){
            //on génère le chemin d'accès
            if ($param[2] == '-'){
                $folder = ROOT . 'views/uploads/img/' . $param[3] . '.' . $param[1];
            }else {
                $folder = ROOT . 'views/uploads/img/' . $param[2] . '/' . $param[3] . '.' . $param[1];
            }
            
            if (is_numeric($param[4]) && is_numeric($param[5])){
                $height = $param[4];
                $width = $param[5];
            }else {
                $controleur_def->addError(130);
                die("Erreur de paramètres (4)");
            }
            //On charge le model
            $controleur_def->loadModel('getimage/getimage_jpg');

            header('Pragma: no-cache');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Expires: Mon, 1 Jan 2020 05:00:00 GMT');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private',false);
            header('Content-type: image/jpg');
             
            imagejpeg(resize($folder, $width, $height));
    
        }else if ($param[1] == "jpeg"){
            //on génère le chemin d'accès
            if ($param[2] == '-'){
                $folder = ROOT . 'views/uploads/img/' . $param[3] . '.' . $param[1];
            }else {
                $folder = ROOT . 'views/uploads/img/' . $param[2] . '/' . $param[3] . '.' . $param[1];
            }
            
            if (is_numeric($param[4]) && is_numeric($param[5])){
                $height = $param[4];
                $width = $param[5];
            }else {
                $controleur_def->addError(130);
                die("Erreur de paramètres (4)");
            }
            //On charge le model
            $controleur_def->loadModel('getimage/getimage_jpg');

            header('Pragma: no-cache');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Expires: Mon, 1 Jan 2020 05:00:00 GMT');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private',false);
            header('Content-type: image/jpeg');
             
            imagejpeg(resize($folder, $width, $height));
    
        }else {
            $controleur_def->addError(130);
            die("Erreur de paramètres (2)");
        }
    }else {
        $controleur_def->addError(131);
        die("Erreur de paramètres (3)");
    }
 
}else {
    $controleur_def->addError(130);
    die("Erreur de paramètres (1)");
}
 
