<?php 

/**
 * clearString - Fonction ayant pour but de "nettoyer" un string 
 * Elle peut être utilisée lors de l'upload d'un fichier par exemple, où l'on veut échapper les caractères sépciaux
 * 
 * @author Aldric L.
 * @copyright 2020
 * @version 1.0
 * @param string $string : le string à nettoyer
 * @param boolean $spaces : Doit-on retirer les espaces ? (par défaut: non)
 * @param boolean $url : Doit-on retirer les url ? (par défaut: non)
 * @param boolean $to_lower : Doit-on retirer les majuscules ? (par défaut: non)
 * @return string Le string est retourné après traitement. Aucune erreur possible.
 */
function clearString($string, $spaces=FALSE, $url=FALSE, $to_lower=FALSE){
    if ($to_lower){
        $string = mb_strtolower($string);
    }
    
    $string = str_replace('é', 'e', $string);
    $string = str_replace('è', 'e', $string);
    $string = str_replace('ê', 'e', $string);
    $string = str_replace('@', 'a', $string);
    
    if ($spaces){
        $string = str_replace(' ', '_', $string);
        $string = str_replace("\ ", '', $string);
    }

    if ($url){
        $string = str_replace(':', '', $string);
        $string = str_replace("/", '', $string);
        $string = str_replace('.', '', $string);
    }
        
    $string = str_replace('[', '', $string);
    $string = str_replace(']', '', $string);
    $string = str_replace('(', '', $string);
    $string = str_replace(')', '', $string);
    $string = str_replace('{', '', $string);
    $string = str_replace('}', '', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace('~', '', $string);
    $string = str_replace('^', '', $string);
    $string = str_replace('$', '', $string);
    $string = str_replace('£', '', $string);
    $string = str_replace('¤', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('µ', '', $string);
    $string = str_replace('¨', '', $string);
    $string = str_replace('ù', '', $string);
    $string = str_replace('§', '', $string);
    $string = str_replace('!', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace(',', '', $string);
    $string = str_replace('?', '', $string);
    $string = str_replace('=', '', $string);
    $string = str_replace('+', '', $string);
    $string = str_replace('°', '', $string);
    $string = str_replace('à', 'a', $string);
    $string = str_replace('ä', 'a', $string);
    $string = str_replace('ö', 'o', $string);
    $string = str_replace('ô', 'o', $string);
    $string = str_replace('ü', 'u', $string);
    $string = str_replace('û', 'u', $string);
    $string = str_replace('ë', 'e', $string);
    $string = str_replace('ç', 'c', $string);
    $string = str_replace('`', '', $string);
    $string = str_replace('|', '', $string);
    $string = str_replace('&', '', $string);
    $string = str_replace('²', '', $string);
    return $string;
}

/**
  * Fonction permettant l'upload de fichiers sur le serveur
  * L'intéret de cette fonction est qu'elle isole tous les types d'erreurs possibles

  * @author Aldric L.
  * @copyright 2020
  * @version 1.0
  * 
  * @param string $file : index du tableau $_FILES
  * @param string $folder : L'emplacement où doit être entreposé le fichier : views/uploads/img/ + $folder (Ce paramètre peut être vide)
  * @param bool $rename : Si l'on modifie le nom du fichier pour y ajouter un identifiant unique
  * 
  * @return int|string si string, le fichier se trouve au bon emplacement slon le nom renvoyé
  * @return int 10 : aucun fichier uploadé
  * @return int 20 ou 21 : upload_max_filesize : phpini maxsize => Fichier trop important (20 => phpini ; 21 => html)
  * @return int 22 : erreur interne inconnue
  * @return int 23 : Aucun fichier uploadé
  * @return int 24 : problème lié à l'extension du fichier
  * @return int 25 : problème d'écriture
  * @return int 30 : encore un problème d'écriture, suite au déplacement du fichier (lié quasiment toujours aux droits d'accès : sous linux inscrire 777)
  */
function uploadFile($file, $folder=null, $rename=true, $path=ROOT . "views/uploads/img/"){
    //Test1: fichier correctement uploadé
    if (!isset($_FILES[$file])){
      return 10;
    }
    //Test2: aucune erreur
    if ($_FILES[$file]['error'] > 0){
      //DEBUG
      //var_dump($_FILES[$file]['error']);
      //die;
      if ($_FILES[$file]['error'] == 1){
           return 20;
      }else if ($_FILES[$file]['error'] == 2){
           return 21;
      }else if ($_FILES[$file]['error'] == 3 || $_FILES[$file]['error'] == 6){
           return 22;
      }else if ($_FILES[$file]['error'] == 4){
           return 23;
      }else if ($_FILES[$file]['error'] == 7){
           return 24;
      }else if ($_FILES[$file]['error'] == 8){
           return 25;
      }
      //Erreur 1 upload_max_filesize : phpini maxsize
      //Erreur 2 Taille trop grande, html
      //Erreur 3 :interne
      //Erreur 4 : Aucun fichier reçu
      //Erreur 6 : Erreur interne (manque un dossier temp)
      //Erreur 7 : Erreur d'ecriture
      //Erreur 8 : Problème d'extention.
    }

    //Déplacement
    $u_id = uniqid();
    $primary_name = clearString($_FILES[$file]['name'], true, false);
    //$primary_name = $_FILES[$file]['name'];
    if ($folder != null){
        if ($rename == false){
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT ."views/uploads/img/" . $folder . "/" . "_" . $primary_name);
        }else {
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT ."views/uploads/img/" . $folder . "/" .  $u_id . "_" . $primary_name);
        }
    
        if ($deplacement){
            if ($rename == false){
                return $folder . "/" . $primary_name;
            }else {
                return $folder . "/". $u_id . "_" . $primary_name;
            }
        }else {
           return 30;
        }
        
    }else {
        if ($rename == false){
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT ."views/uploads/img/" . $primary_name);
        }else {
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT ."views/uploads/img/" .  $u_id . "_" . $primary_name);
        }
        if ($deplacement){
            if ($rename == false){
                return $primary_name;
            }else {
                return $u_id . "_" . $primary_name;
            }
        }else {
           return 30;
        }
    }

}


/**
* Converts bytes into human readable file size.
*
* @param string $bytes
* @return string human readable file size (2,87 Мб)
* @author Mogilev Arseny (DOCUMENTATION PHP)
*/
function FileSizeConvert($bytes)
{
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    $result = 0;

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}

/**
 * rrmdir - Fonction pour supprimer de manière récursive un dossier et son contenu
 * 
 * @author Aldric L.
 * @copyright 2020
 * @param string $dir : Le chemin d'accès au dossier
 * @return void
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir."/".$object) == "dir"){
             rrmdir($dir."/".$object);
          }else{ 
             unlink($dir."/".$object);
          }
        }
      }
      reset($objects);
      rmdir($dir);
   }
 }
