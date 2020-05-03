<?php 
/**
  * Fonction permettant l'upload de fichiers sur le serveur
  * L'intéret de cette fonction est qu'elle isole tous les types d'erreurs possibles

  * @author Aldric L.
  * @copyright 2020
  * @version 1.0
  * 
  * @param string $file : index du tableau $_FILESL
  * @param string $folder : L'emplacement où doit être entreposé le fichier : views/uploads/img/ + $folder (Ce paramètre peut être vide)
  * @param bool $rename : Si l'on modifie le nom du fichier pour y ajouter un identifiant unique
  * 
  * @return int|string si string, le fichier se trouve au bon emplacement slon le nom renvoyé
  * @return int 10 : aucun fichier uploadé
  * @return int 20 ou 21 : upload_max_filesize : phpini maxsize => Fichier trop important (20 => phpin ; 21 => html)
  * @return int 22 : erreur interne inconnue
  * @return int 23 : Aucun fichier uploadé
  * @return int 24 : problème lié à l'extension du fichier
  * @return int 25 : problème d'écriture
  * @return int 30 : encore un problème d'écriture, suite au déplacement du fichier (lié quasiment toujours aux droits d'accès : sous linux inscrire 777)
  */
function uploadFile($file, $folder=null, $rename=true){
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
    if ($folder != null){
        $u_id = uniqid();
        $primary_name = str_replace(" ", "_", $_FILES[$file]['name']);
        $primary_name = str_replace("é", "_", $primary_name);
        $primary_name = str_replace("è", "_", $primary_name);
        $primary_name = str_replace("ç", "_", $primary_name);
        $primary_name = str_replace("&", "_", $primary_name);
        $primary_name = str_replace("à", "_", $primary_name);
        $primary_name = str_replace("@", "_", $primary_name);
        if ($rename == false){
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT .'views/uploads/img/' . $folder . '/' . "_" . $primary_name);
        }else {
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT .'views/uploads/img/' . $folder . '/' .  $u_id . "_" . $primary_name);
        }
    
        if ($deplacement){
            if ($rename == false){
                return $folder . '/' . $primary_name;
            }else {
                return $folder . '/'. $u_id . "_" . $primary_name;
            }
        }else {
           return 30;
        }
        
    }else {
        $u_id = uniqid();
        $primary_name = str_replace(" ", "_", $_FILES[$file]['name']);
        $primary_name = str_replace("é", "_", $primary_name);
        $primary_name = str_replace("è", "_", $primary_name);
        $primary_name = str_replace("ç", "_", $primary_name);
        $primary_name = str_replace("&", "_", $primary_name);
        $primary_name = str_replace("à", "_", $primary_name);
        $primary_name = str_replace("@", "_", $primary_name);
        if ($rename == false){
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT .'views/uploads/img/' . $primary_name);
        }else {
            $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
            ROOT .'views/uploads/img/' .  $u_id . "_" . $primary_name);
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