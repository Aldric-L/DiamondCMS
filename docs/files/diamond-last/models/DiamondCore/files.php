<?php 

/**
 * clearString - Fonction ayant pour but de récupérer un filter pour nettoyer un string
 * Elle peut être utilisée lors de l'upload d'un fichier par exemple, où l'on veut échapper les caractères sépciaux
 * 
 * @author Aldric L.
 * @copyright 2020-2023
 * @version 1.0
 * @param boolean $spaces : Doit-on retirer les espaces ? (par défaut: non)
 * @param boolean $url : Doit-on retirer les url ? (par défaut: non)
 * @param boolean $to_lower : Doit-on retirer les majuscules ? (par défaut: non)
 * @return array $filter : Le filtre en tableau associatif
 */
function getClearStringFilter($spaces=FALSE, $url=FALSE){
    $filter = array(
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        '@' => 'a',
        '[' => '',
        ']' => '',
        '(' => '',
        ')' => '',
        '{' => '',
        '}' => '',
        "'" => '',
        '"' => '',
        '~' => '',
        '^' => '',
        '$' => '',
        '£' => '',
        '¤' => '',
        '*' => '',
        'µ' => '',
        '¨' => '',
        'ù' => '',
        '§' => '',
        '!' => '',
        ';' => '',
        ',' => '',
        '?' => '',
        '=' => '',
        '+' => '',
        '°' => '',
        'à' => 'a',
        'ä' => 'a',
        'ö' => 'o',
        'ô' => 'o',
        'ü' => 'u',
        'û' => 'u',
        'ë' => 'e',
        'ç' => 'c',
        '`' => '',
        '|' => '',
        '&' => '',
        '²' => '',
        '>' => '',
        '<' => '',
        );
    
    
    if ($spaces){
        $filter = array_merge($filter, array(
            ' ' => '_',
            "\ " => ''
        ));
    }

    if ($url){
        $filter = array_merge($filter, array(
            ':' => '',
            "/" => '',
            "." => '',
        ));
    }

    return $filter;
}


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
    
    $filter = getClearStringFilter($spaces, $url, $to_lower);
    foreach ($filter as $key => $f){
        $string = str_replace($key, $f, $string);
    }

    /*
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
    $string = str_replace('²', '', $string);*/


    return $string;
}

/**
 * Fonction permettant de modifier les métadonnées du système pour débloquer un fichier
 * @author Aldric L.
 * @copyright 2023
 * @version 1.0
 * 
 * @param string $path : le path du dossier dans lequel est le fichier
 * @param string $filename : le nom du fichier avec extension
 * @param array $flags : les paramètres à écrire en métadonnées
 * @return void
 */
function unlock_file(string $path, string $filename, array $flags=array("locked"=>false)) : void{
    if (file_exists($path . "locked_files.dfiles")){
        $conf = json_decode(file_get_contents($path . "locked_files.dfiles"), true);
        if (is_array($conf) && array_key_exists($filename, $conf) && is_array($conf[$filename])){
            if (is_array($flags)){
                foreach ($flags as $key => $f){
                    if (array_key_exists($key, $conf[$filename]) && $conf[$filename][$key])
                        $conf[$filename][$key] = $f;   
                }
                file_put_contents($path . "locked_files.dfiles", json_encode($conf));
            }
        }
    }
}

/**
  * Fonction permettant l'upload de fichiers sur le serveur
  * L'intéret de cette fonction est qu'elle isole tous les types d'erreurs possibles
  * Attention, il s'agit d'une version modifiée pour DiamondCMS en incluant les permissions
  *
  * @author Aldric L.
  * @copyright 2020-2023
  * @version 1.2
  * 
  * @param string $file : index du tableau $_FILES
  * @param string $folder : L'emplacement où doit être entreposé le fichier : views/uploads/img/ + $folder (Ce paramètre peut être vide)
  * @param bool $rename : Si l'on modifie le nom du fichier pour y ajouter un identifiant unique
  * @param array/null $extensions : array contenant les extensions autorisées
  * @param array $flags : array contenant les options à écrire sur le fichier dans le système (protected, protected_name, access_level)
  * 
  * @return int|string si string, le fichier se trouve au bon emplacement slon le nom renvoyé
  * @return int 10 : aucun fichier uploadé
  * @return int 20 ou 21 : upload_max_filesize : phpini maxsize => Fichier trop important (20 => phpini ; 21 => html)
  * @return int 22 : erreur interne inconnue
  * @return int 23 : Aucun fichier uploadé
  * @return int 24 : problème lié à l'extension du fichier
  * @return int 25 : problème d'écriture
  * @return int 30 : encore un problème d'écriture, suite au déplacement du fichier (lié quasiment toujours aux droits d'accès : sous linux inscrire 777)
  * @return int 31 : pas la permission d'écrire dans le dossier
  * @return int 32 : extension invalide
  */
function uploadFile($file, $folder=null, $rename=true, $path=ROOT . "views/uploads/img/", $extensions = null, $flags = array("access_level" => 1, "protected" => false, "protected_name" => true), $level=null){
    $level_min = 1;
    $private_path = $path;
    if ($private_path[strlen($private_path)-1] != '/')
        $private_path .= "/";
    if (!is_null($folder))
        $private_path = $path . $folder . "/";
    if (!file_exists($private_path . "locked_files.dfiles") && file_exists("/" . $private_path . "locked_files.dfiles"))
        $private_path = "/" . $private_path;

    if (file_exists($private_path . "locked_files.dfiles")){
        $conf = json_decode(file_get_contents($private_path . "locked_files.dfiles"), true);
        if (is_array($conf) && array_key_exists("__GLOBAL-FOLDER-DIAMONDCONF__", $conf) && is_array($conf["__GLOBAL-FOLDER-DIAMONDCONF__"])){
            if (array_key_exists("access_level", $conf["__GLOBAL-FOLDER-DIAMONDCONF__"]) && is_numeric($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"])){
                $level_min = intval($conf["__GLOBAL-FOLDER-DIAMONDCONF__"]["access_level"]);         
            }        
        }       
    }
    if (is_null($level)){
        if ($level_min > 1 && (!isset($_SESSION["user"]) or !($_SESSION["user"] instanceof User) or (isset($_SESSION["user"]) && $_SESSION["user"] instanceof User && $_SESSION["user"]->getLevel() < $level_min)))
            return 31;
    }else if (is_int($level)){
        if ($level_min > 1 && $level < $level_min)
            return 31;
    }else {
        return 31;
    }

    if (is_array($flags) && array_key_exists("access_level", $flags) && $flags["access_level"] < $level_min)
        $flags["access_level"] = $level_min;

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

    function check_and_write_metaconf($path, $file_name, $flags){
        if (is_dir($path)){
            if (file_exists($path . "locked_files.dfiles"))
                $conf = json_decode(file_get_contents($path . "locked_files.dfiles"), true);
            else
                $conf = array();

            if (is_array($conf) && !array_key_exists($file_name, $conf)){
                $conf[$file_name] = $flags;
                file_put_contents($path . "locked_files.dfiles", json_encode($conf));
            }
        }
    }

    //Déplacement
    $u_id = uniqid();
    $primary_name_array = explode('.', $_FILES[$file]['name']);
    $extension = mb_strtolower(array_pop($primary_name_array));

    if (is_array($extensions) && !in_array($extension, $extensions))
        return 32;

    $primary_name = clearString(implode(".", array_filter($primary_name_array)), true, true);
    $name = ($rename) ? $u_id . "_" . $primary_name . "." . $extension : $primary_name . "." . $extension;
    $final_path = ($folder != null) ? $path . $folder . "/" : $path;
    //$primary_name = $_FILES[$file]['name'];
    $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], $final_path . $name);

    if ($deplacement){
        check_and_write_metaconf($final_path, $name, $flags);
        if ($folder != null){
            return $folder . "/". $name;
        }else {
            return $name;
        }
    }
    return 30;
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
