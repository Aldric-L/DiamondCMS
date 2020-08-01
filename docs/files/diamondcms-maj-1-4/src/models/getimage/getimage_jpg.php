<?php 
/**
 * Ce modèle permet de charger une image à la dimension voulue (il rogne l'image au besoin)
 * Attention, il nécessite phpGD pour fonctionner
 *
 * @author Le code est principalement issu de mon-beulogne.com (https://mon-beulogue.com/2007/06/11/redimensionner-et-rogner-une-image-en-php/)
 * Il est donc sous licence libre, et n'appartient pas à Aldric L. qui l'a simplement adapté au projet DiamondCMS
 *
 * Attention il s'agit de la version pour les .jpg (extraite du site)
 *
 */
 
 function resize( $folder, $width, $height ){
    // récupération de la taille de l'image d'origine
    list($currentWidth, $currentHeight) = getimagesize($folder);
    $calW = ceil(($height/$currentHeight)*$currentWidth);
    $calH = ceil(($width/$currentWidth)*$currentHeight);
 
    $ratioW = ($calW == 0 || $width == 0)? 0 : $calW % $width;
    $ratioH = ($calH == 0 || $height == 0)? 0 : $calH % $height;
 
    if($ratioW < 10 && $ratioH < 10) {
        return img_resize($folder, $width, $height);
    } else {
        $img = img_resize_auto($folder, $width, $height);
        return img_rogne_resize($folder, $width, $height, $img);
    }
}
 
/**
 * Redimensionne une image pour une largeur fixée
 */
function img_resize_x ( $filename, $maxWidth, $maxHeight ) {
    // récupération de la taille de l'image d'origine
    list($currentWidth, $currentHeight) = getimagesize($filename);
    $ratio = $currentWidth / $currentHeight;
    $newWidth = $maxWidth;
    $newHeight = round($newWidth / $ratio);
    $newHeight = ($maxHeight > $newHeight)? $maxHeight : $newHeight;
 
    return img_resize($filename, $newWidth, $newHeight);
} // end of 'img_resize_x()'
 
/**
 * Redimensionne une image pour une hauteur fixée
 */
function img_resize_y ( $filename, $maxHeight, $maxWidth ) {
    // récupération de la taille de l'image d'origine
    list($currentWidth, $currentHeight) = getimagesize($filename);
    $ratio = $currentWidth / $currentHeight;
    $newHeight = $maxHeight;
    $newWidth = round($newHeight * $ratio);
    $newWidth = ($maxWidth > $newWidth)? $maxWidth : $newWidth;
 
    return img_resize($filename, $newWidth, $newHeight);
} // end of 'img_resize_y()'
 
function img_resize_auto( $filename, $maxW, $maxH ) {
    // récupération de la taille de l'image d'origine
    list($width, $height) = getimagesize($filename);
 
    if ($maxW > $maxH) {
        return img_resize_x($filename, $maxW, $maxH);
    } else {
        return img_resize_y($filename, $maxH, $maxW);
    }
} // end of 'img_resize_auto()'
 
/**
 * Retourne l'image redimentionnée
 */
function img_resize( $filename, $newWidth, $newHeight ) {
    // récupération de la taille de l'image d'origine
    list($currentWidth, $currentHeight) = getimagesize($filename);
 
    // Création de la miniature
    $srcImg = @imagecreatefromjpeg($filename);
    if ( !$srcImg ) {
        $im = imagecreate(150, 30); // Création d'une image blanche
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        // Affichage d'un message d'erreur
        imagestring($im, 1, 5, 5, "Erreur de chargement de l'image ".basename($filename), $tc);
        return $im;
    }
 
    $dstImg = @imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresized($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);
 
    return $dstImg;
} // end of 'img_resize()'
 
/**
 * Retourne une image rognée
 */
function img_rogne_resize($filename, $width, $height, $image = FALSE){
    // récupération de la taille de l'image d'origine
    list($width_orig, $height_orig) = getimagesize($filename);
    $height_orig2=$height_orig/($width_orig/$width);
 
    $image_p = imagecreatetruecolor($width, $height);
    if(!$image) {
        $image = imagecreatefromjpeg($filename);
    }
 
    imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
 
    return $image_p;
}