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

//Désormais, si il n'y qu'une dimension spécifiée, on considère que l'on souhaite du 16/9, à partir de la longueur
if (isset($param[1]) && !empty($param[1]) && 
    isset($param[2]) && !empty($param[2]) &&
    isset($param[3]) && !empty($param[3]) && 
    isset($param[4]) && !empty($param[4]) && 
    (!isset($param[5]) || empty($param[5])) ){

    $param[5] = $param[4];
    $param[4] = round($param[5] * (9 / 16));
}
    

 //Si tous les paramètres sont bien spécifiés
if (isset($param[1]) && !empty($param[1]) && 
    isset($param[2]) && !empty($param[2]) &&
    isset($param[3]) && !empty($param[3]) ){
    
    //si le fichier existe à l'emplacement indiqué
    if (($param[2] == '-' && file_exists(ROOT . 'views/uploads/img/' . $param[3] . '.' . $param[1])) || file_exists(ROOT . 'views/uploads/img/' . $param[2] . '/' . $param[3] . '.' . $param[1])){
        //on génère le chemin d'accès
        $target = $param[3] . '.' . $param[1];
        if ($param[2] == '-'){
            $path = ROOT . 'views/uploads/img/';
            $folder = ROOT . 'views/uploads/img/' . $param[3] . '.' . $param[1];
        }else {
            $path = ROOT . 'views/uploads/img/' . $param[2] . '/';
            $folder = ROOT . 'views/uploads/img/' . $param[2] . '/' . $param[3] . '.' . $param[1];
        }

        if (!Manager::canIShowThisFile($folder, (isset($_SESSION["user"]) and $_SESSION["user"] instanceof User) ? $_SESSION["user"] : null)){
            if (file_exists($folder = ROOT . 'views/uploads/img/img_blocked.jpg')){
                $param[1] = "jpg";
                $path = ROOT . 'views/uploads/img/';
                $param[2] = '-';
                $param[3] = 'img_blocked';
            }else {
                die("Forbidden access");
            }
        }
            

        if (isset($param[4]) && !empty($param[4]) && isset($param[5]) && !empty($param[5]) && is_numeric($param[4]) && is_numeric($param[5])){
            $height = intval($param[4]);
            $width = intval($param[5]);
        }else if (empty($param[4]) && empty($param[5])){
            $height = $width = false;
        }else {
            $controleur_def->addError(130);
            die("Erreur de paramètres (4)");
        }

        if ($height == $width && $width == false)
            $cache_name = $param[3] . "_" . $param['1'];
        else
            $cache_name = $param[3] . "_" . $param['1'] . "_" . $param[4] . "_" . $param[5];

        $img_cache = new DiamondCache(ROOT . "tmp/img/", 10);

        if ($param[1] == "png"){
            //On charge le model
            $controleur_def->loadModel('getimage/getimage_png');

            header('Pragma: no-cache');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-type: image/png');
            
            if ($c = $img_cache->read($cache_name . ".dcms") === false){
                ob_start();
                if ($height == $width && $width == false)
                    echo file_get_contents($folder);
                else
                    imagepng(resize($folder, $width, $height));
                $data = ob_get_clean();
                $img_cache->write($cache_name . ".dcms", $data);
                echo $data;
            }else {
                //On ne peut pas utiliser le read du cache car celui-ci ne supporte pas les images
                //imagepng(imagecreatefrompng($img_cache->get_path() . $cache_name . ".dcms"));
                echo file_get_contents($img_cache->get_path() . $cache_name . ".dcms");
            }
        }else if ($param[1] == "jpg"){
            //On charge le model
            $controleur_def->loadModel('getimage/getimage_jpg');

            header('Pragma: no-cache');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-type: image/jpg');

            if ($c = $img_cache->read($cache_name . ".dcms") === false){
                ob_start();
                if ($height == $width && $width == false)
                    echo file_get_contents($folder);
                else
                    imagejpeg(resize($folder, $width, $height));
                $data = ob_get_clean();
                $img_cache->write($cache_name . ".dcms", $data);
                echo $data;
            }else {
                //On ne peut pas utiliser le read du cache car celui-ci ne supporte pas les images
                //imagejpeg(imagecreatefromjpeg($img_cache->get_path() . $cache_name . ".dcms"));
                echo file_get_contents($img_cache->get_path() . $cache_name . ".dcms");
            }
    
        }else if ($param[1] == "jpeg"){
            //On charge le model
            $controleur_def->loadModel('getimage/getimage_jpg');
            
            header('Pragma: no-cache');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-type: image/jpeg');

            if ($c = $img_cache->read($cache_name . ".dcms") === false){
                ob_start();
                if ($height == $width && $width == false)
                    echo file_get_contents($folder);
                else
                    imagejpeg(resize($folder, $width, $height));
                $data = ob_get_clean();
                $img_cache->write($cache_name . ".dcms", $data);
                echo $data;
            }else {
                //On ne peut pas utiliser le read du cache car celui-ci ne supporte pas les images
                //imagejpeg(imagecreatefromjpeg($img_cache->get_path() . $cache_name . ".dcms"));
                echo file_get_contents($img_cache->get_path() . $cache_name . ".dcms");
            }
    
        }else if (mb_strtolower($param[1]) == "gif" OR mb_strtolower($param[1]) == "bmp" OR mb_strtolower($param[1]) == "ico"){
            $extension = pathinfo($folder);
            $extension = array_key_exists("extension", $extension) ? $extension['extension'] : "";
            header('Pragma: no-cache');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            if (mb_strtolower($param[1]) == "ico"){
                header('Content-type: image/x-icon');
            }else {
                header('Content-type: image/' . $extension);
            }

            echo file_get_contents($folder);
    
        }else {
            $controleur_def->addError(130);
            die("Erreur de paramètres (2)");
        }
    }else {
        $controleur_def->addError(131);
        if (isset($param[4]) && !empty($param[4]) && isset($param[5]) && !empty($param[5]))
            header('Location: ' . LINK . 'getimage/png/profiles/no_profile/' . $param[4] . '/' . $param[5]);
        else 
            header('Location: ' . LINK . 'getimage/png/profiles/no_profile/');
        //die("Erreur de paramètres (3)");
    }
 
}else {
    $controleur_def->addError(130);
    die("Erreur de paramètres (1)");
}
 
