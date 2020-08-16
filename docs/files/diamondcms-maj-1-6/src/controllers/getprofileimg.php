<?php 
/**
 * Ce controlleur permet de charger la photo de profil demandée à partir de la taille précisée en paramètres
 * Puisqu'il a pour but de dialoguer aussi avec l'addon Diamond-MinecraftProfileImg, il permet aussi de récuperer le skin entier
 *
 * @author Aldric L. 2020
 * Pour fonctionner ce controller a besoin de :
 * $param[1] qui représente le nom de l'utilisateur
 * $param[2] qui représente la hauteur de l'image (écrire 0 pour un skin)
 * $param[3] qui réprésente la largeur de l'image (il n'est pas obligatoire)
 * $param[4] qui réprésente l'orientation de l'image pour les tetes minecraft (il n'est pas obligatoire) => si on veut un skin, écrire "skin"
 * 
*/
if (isset($param[1]) && !empty($param[1]) && isset($param[2])){
    
    if (defined("DMcProfileImg") && DMcProfileImg == true){
        if (isset($param[3]) && !empty($param[3]) && (!isset($param[4]) || empty($param[4])) ){
            $user_g = $param[1];
            $size_g = $param[2];
            require_once ROOT . 'addons/Diamond-MinecraftProfileImg/src/face.php';
        }else if (isset($param[3]) && !empty($param[3]) && isset($param[4]) && !empty($param[4])){
            //var_dump($param);
            if ($param[2] == "0" && $param[4] == "skin"){
                $user_g = $param[1];
                $size_g = intval($param[3]);
                require_once ROOT . 'addons/Diamond-MinecraftProfileImg/src/skin.php';
            }else {
                $user_g = $param[1];
                $size_g = $param[2];
                $view_g = $param[4];
                require_once ROOT . 'addons/Diamond-MinecraftProfileImg/src/face.php';
            }
        }else {
            $user_g = $param[1];
            $size_g = $param[2];
            require_once ROOT . 'addons/Diamond-MinecraftProfileImg/src/face.php';
        }
    }else if (isset($param[3]) && !empty($param[3])) {
        if ($param[1] == "Utilisateur inconnu"){
            header('Location: ' . LINK . 'getimage/png/profiles/no_profile/' . $param[2] . '/'. $param[3]);
        }else {
            $user = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "profile_img", array(array("pseudo", "=", $param[1])));
            if (!empty($user) && isset($user['profile_img'])){
                if (strpos($user['profile_img'], "png") !== false) {
                    header('Location: ' . LINK . 'getimage/png/' . substr($user['profile_img'], 0, -4) . '/' . $param[2] . '/'. $param[3]);
                }else if (strpos($user['profile_img'], "jpg") !== false) {
                    header('Location: ' . LINK . 'getimage/jpg/' . substr($user['profile_img'], 0, -4) . '/' . $param[2] . '/'. $param[3]);
                }else if (strpos($user['profile_img'], "jpeg") !== false) { 
                    header('Location: ' . LINK . 'getimage/jpeg/' . substr($user['profile_img'], 0, -5) . '/' . $param[2] . '/'. $param[3]);
                }else {
                    $controleur_def->addError(130);
                    die('Erreur');
                }
            }else {
                header('Location: ' . LINK . 'getimage/png/profiles/no_profile/' . $param[2] . '/'. $param[3]);     
            }
        }
        
    }else {
        if ($param[1] == "Utilisateur inconnu"){
            header('Location: ' . LINK . 'getimage/png/profiles/no_profile/' . $param[2] . '/'. $param[2]);
        }else {
            $user = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "profile_img", array(array("pseudo", "=", $param[1])));
            if (!empty($user) && isset($user['profile_img'])){
                if (strpos($user['profile_img'], "png") !== false) {
                    header('Location: ' . LINK . 'getimage/png/' . substr($user['profile_img'], 0, -4) . '/' . $param[2] . '/'. $param[2]);
                }else if (strpos($user['profile_img'], "jpg") !== false) {
                    header('Location: ' . LINK . 'getimage/jpg/' . substr($user['profile_img'], 0, -4) . '/' . $param[2] . '/'. $param[2]);
                }else if (strpos($user['profile_img'], "jpeg") !== false) { 
                    header('Location: ' . LINK . 'getimage/jpeg/' . substr($user['profile_img'], 0, -5) . '/' . $param[2] . '/'. $param[2]);
                }else {
                    $controleur_def->addError(130);
                    die('Erreur');
                }
            }else {
                header('Location: ' . LINK . 'getimage/png/profiles/no_profile/' . $param[2] . '/'. $param[2] );     
            }
        }
    }
}
