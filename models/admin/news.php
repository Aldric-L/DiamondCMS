<?php 
function uploadFile(){
     if (!isset($_FILES[$file])){
       return 10;
       //Test1: fichier correctement uploadé
     }
     if ($_FILES[$file]['error'] > 0){
       //DEBUG
       //var_dump($_FILES[$file]['error']);
       //die;
       if ($_FILES[$file]['error'] == 1 || $_FILES[$file]['error'] == 2 || $_FILES[$file]['error'] == 3 || $_FILES[$file]['error'] == 6 || $_FILES[$file]['error'] == 7){
            return 511;
       }else if ($_FILES[$file]['error'] == 8){
            return 512;
       }else if ($_FILES[$file]['error'] == 4){
            return 513;
       }
       //Test2: aucune erreur
       //Erreur 1 upload_max_filesize : phpini maxsize
       //Erreur 2 Taille trop grande, html
       //Erreur 3 :interne
       //Erreur 4 : Aucun fichier reçu
       //Erreur 6 : Erreur interne (manque un dossier temp)
       //Erreur 7 : Erreur d'ecriture
       //Erreur 8 : Problème d'extention.
     }

     if (file_exists(ROOT .'views/uploads/img/' . $_FILES[$file]['name'])){
      //Déplacement
      //ATTENTION : vérifier si un dossier existe pour chaque classe
      $u_id = uniqid();
      $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
      ROOT .'views/uploads/img/' .  $u_id . "_" . $_FILES[$file]['name']);

      if ($deplacement){
        return $u_id . "_" . $_FILES[$file]['name'];
      }else {
        /* En cas de soucil, 99% des fois, vérifié les droits d'acces : mettre tout en 777 */
        //var_dump($deplacement);
        return 514;
      }
     }else {
      //Déplacement
      //ATTENTION : vérifier si un dossier existe pour chaque classe
      $deplacement = move_uploaded_file($_FILES[$file]['tmp_name'], 
      ROOT .'views/uploads/img/' . $_FILES[$file]['name']);

      if ($deplacement){
         return $_FILES[$file]['name'];
      }else {
        /* En cas de soucil, 99% des fois, vérifié les droits d'acces : mettre tout en 777 */
        //var_dump($deplacement);
        return 514;
      }
     }

}

function delNews($db, $id){
  return $db->exec("DELETE FROM d_news WHERE id = " . $id);
}