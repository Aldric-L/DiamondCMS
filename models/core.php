<?php 
namespace simplifySQL {

/**
  * Fonction de simplification, pour les requettes SQL de type SELECT
  * Cette fonction respecte les bonnes pratiques quant à l'utilisation de PDO, elle limite les failles SQL. Elle est donc a utiliser au maximum.

  * @author Aldric L.
  * @copyright 2017-2018
  * @version 1.1
  * 
  * @param boolean $fetch Type de récupération : fetch OU fetchall si $fetch = true alors fetch sinon fetchAll
  * @param string $from table SQL
  * @param array $wanted Elements recupérés par la requete
  * Syntaxe : soit un string "elem1, elem2" soit un array : array("elem1", "elem2", array("date_bdd", "format", "as"))
  * @param array|bool $where Condition(s) à effectuer dans la requete SQL, si false alors ignorée
  * Syntaxe : $where = array(array("nom_du_champ", "egalite", "valeur"), "OR", array("nom_du_champ", "egalite", "valeur"));
  * @param string|bool $order_by Nom du champ sur le-quel appliqué le classement des élements par ordre croissant
  * @param boolean $desc si true : Ordre décroissant des élements
  * @param array|bool $limit premier élement du tableau : minimum, deuxième : limite; false si ignoré
  *
  * @return array|bool si false, faire un débogage complet, sinon execution normale de la fonction.
  */

function select($db, $fetch, $from, $wanted, $where=false, $order_by=false, $desc=false, $limit=false){
    $request = "SELECT ";
    
    if (is_array($wanted)){
        //Partie arguments (Ce qui est selectionné)
        for ($i = 0; $i <= sizeof($wanted); $i++){
            //Si il ya un sous tableau, c'est une date
            if (array_key_exists($i, $wanted) && is_array($wanted[$i])){
                $request .= "DATE_FORMAT(";
                $request .= $wanted[$i][0];
                $request .= ", ";
                $request .= "'" . $wanted[$i][1] . "') AS ";
                $request .=  $wanted[$i][2];
                if ($i+1 != sizeof($wanted)){
                    $request .= ", ";
                }else {
                    $request .= " ";
                }
            //Sinon, comportement normal, on liste les paramètres
            }else if (array_key_exists($i, $wanted)){
                $request .= $wanted[$i];
                if (($i+1) < sizeof($wanted)){
                    $request .= ", ";
                }else {
                    $request .= " ";
                }
            }
        }
    }else {
        $request .= $wanted;
        $request .= " ";
    }
    //Partie FROM
    $request .= "FROM ";
    $request .= $from;
    $request .= " ";

    $bindparams = array();

    //Partie Where
    if ($where != false && is_array($where)){
        // where = array(array("nom_du_champ", "egalite"), "OR", array("nom_du_champ", "egalite"));
        $request .= "WHERE ";
        //var_dump($where);
        for ($i = 0; $i <= sizeof($where); $i++){
            //Si c'est une condition
            //var_dump(is_array($where[$i]), $where[$i], $where[$i][0]);
            if (array_key_exists($i, $where) && is_array($where[$i])){
                $request .= $where[$i][0];
                $request .= " ";
                $request .= $where[$i][1];
                $request .= " :";
                $request .= $where[$i][0];
                $request .= " ";
                //On ajoute le contenu a tester, pour le passer en bindparam
                array_push($bindparams, array($where[$i][0], $where[$i][2]));
                //Si c'est un opérateur
            }else if (array_key_exists($i, $where)){
                $request .= $where[$i];
                $request .= " ";
            }
            
        }
    }
    //Order by
    if ($order_by != false){
        $request .= "ORDER BY ";
        $request .= $order_by;
        $request .= " ";
    }
    if ($desc != false){
        $request .= "DESC ";
    }
    if ($limit != false && is_array($limit)){
        $request .= "LIMIT :min, :limite ";
    }
    //var_dump($request, $bindparams);
    //Preparation de la requete
    $req = $db->prepare($request);
    // Ajout des bindparams (ceux de la clause WHERE)
    if (sizeof($bindparams) > 0){
        for ($i = 0; $i < sizeof($bindparams); $i++){
            if (is_int($bindparams[$i][1])){
                $req->bindParam($bindparams[$i][0], $bindparams[$i][1], \PDO::PARAM_INT);
            }else if (is_string($bindparams[$i][1])){
                $req->bindParam($bindparams[$i][0], $bindparams[$i][1], \PDO::PARAM_STR);
            }else if (is_bool($bindparams[$i][1])){
                $req->bindParam($bindparams[$i][0], $bindparams[$i][1], \PDO::PARAM_BOOL);
            }else {
                return false;
            }
        }
    }
    //Ajout des bindparams (ceux de la clause LIMIT)
    if ($limit != false && is_array($limit)){
        $req->bindParam("min", $limit[0], \PDO::PARAM_INT);
        $req->bindParam("limite", $limit[1], \PDO::PARAM_INT);
    }
    
    //On execute la requete
    $req->execute();

    if ($fetch){
        //On ne récupère, généralement, qu'un seul élement
        $post = $req->fetch();
    }else {
        //On récupère tout
        $post = $req->fetchAll();
    }

    //On ferme la requete
    $req->closeCursor();
    
    return $post;
}

/**
  * Fonction de simplification, pour les requettes SQL de type INSERT
  * Cette fonction respecte les bonnes pratiques quant à l'utilisation de PDO, elle limite les failles SQL. Elle est donc a utiliser au maximum.

  * @author Aldric L.
  * @copyright 2020
  * @version 1.0
  * 
  * @param string $into table SQL
  * @param array $wanted nom des colonnes modifiées
  * @param array|bool $values valeurs à insérer
  *
  * Attention, cette fonction ne fonctionne que s'il y a correspondance parfaite entre les colonnes de $wantes, et les valeurs de $values qui y seront insérées
  * Exemple : $wanted=array("col1","col2") et $values=array("valeur_de_la_col1", "valeur_de_la_col2")
  *
  * @return bool si false, faire un débogage complet, sinon execution normale de la fonction.
  */

  function insert($db, $into, $wanted, $values){
    //On commence par vérifier que l'on annonce le même nombre de données que l'on va bien envoyer
    if (sizeof($wanted) != sizeof($values)){
        return false;
    }

    $request = "INSERT ";
    
    //Partie INTO
    $request .= "INTO ";
    $request .= $into;
    $request .= " ";

    //PARTIE WANTED
    $request .= "(";
    //S'il n'y a qu'un argument
    if (is_array($wanted)){
        //Partie arguments (Ce qui est envoyé)
        for ($i = 0; $i < sizeof($wanted); $i++){
            $request .= $wanted[$i];
            //S'il s'agit du dernier élèment de la requette
            if ($i+1 == sizeof($wanted)){
                $request .= ")";
            }else {
                $request .= ", ";
            }
        }
    }else {
        $request .= $wanted;
        $request .= ") ";
    }

    //On recommence pour annoncer à PDO les bindParams
    $bindparams = array();

    $request .= "VALUES (";
     //S'il n'y a qu'un argument
     if (is_array($wanted)){
        //Partie arguments (Ce qui est envoyé)
        for ($i = 0; $i < sizeof($wanted); $i++){
            //Sauf qu'ici on insère ":" avant chaque élement puisqu'il s'agit d'un bindParam
            $request .= ":";
            $request .= $wanted[$i];

            //On l'ajoute dans la liste de nos bindparams
            array_push($bindparams, $wanted[$i]);

            //S'il s'agit du dernier élèment de la requette
            if ($i+1 == sizeof($wanted)){
                $request .= ")";
            }else {
                $request .= ", ";
            }
        }
    }else {
        $request .= $wanted;
        $request .= ") ";
    } 

    //Preparation de la requete
    $req = $db->prepare($request);
    // Ajout des bindparams dont la valeur provient de la variable $values
    if (sizeof($bindparams) > 0){
        for ($i = 0; $i < sizeof($bindparams); $i++){
            if (is_int($values[$i])){
                $req->bindParam($bindparams[$i], $values[$i], \PDO::PARAM_INT);
            }else if (is_string($values[$i])){
                $req->bindParam($bindparams[$i], $values[$i], \PDO::PARAM_STR);
            }else if (is_bool($values[$i])){
                $req->bindParam($bindparams[$i], $values[$i], \PDO::PARAM_BOOL);
            }else {
                return false;
            }
        }
    }
    
    //On execute la requete
    return $req->execute();
}

/**
  * Fonction de simplification, pour les requettes SQL de type DELETE
  * Cette fonction respecte les bonnes pratiques quant à l'utilisation de PDO, elle limite les failles SQL. Elle est donc a utiliser au maximum.

  * @author Aldric L.
  * @copyright 2020
  * @version 1.0
  * 
  * @param string $from table SQL
  * @param array|bool $where Condition(s) à effectuer dans la requete SQL, si false alors ignorée
  * Syntaxe : $where = array(array("nom_du_champ", "egalite", "valeur"), "OR", array("nom_du_champ", "egalite", "valeur"));ant
  * @param boolean $desc si true : Ordre décroissant des élements
  *
  * @return bool si false, faire un débogage complet, sinon execution normale de la fonction.
  */

  function delete($db, $from, $where=false){
    $request = "DELETE ";
    
    //Partie FROM
    $request .= "FROM ";
    $request .= $from;
    $request .= " ";

    $bindparams = array();

    //Partie Where
    if ($where != false && is_array($where)){
        // where = array(array("nom_du_champ", "egalite"), "OR", array("nom_du_champ", "egalite"));
        $request .= "WHERE ";
        //var_dump($where);
        for ($i = 0; $i <= sizeof($where); $i++){
            //Si c'est une condition
            //var_dump(is_array($where[$i]), $where[$i], $where[$i][0]);
            if (array_key_exists($i, $where) && is_array($where[$i])){
                $request .= $where[$i][0];
                $request .= " ";
                $request .= $where[$i][1];
                $request .= " ?";
                //On ajoute le contenu a tester, pour le passer en bindparam
                array_push($bindparams, $where[$i][2]);
                //Si c'est un opérateur
            }else if (array_key_exists($i, $where)){
                $request .= $where[$i];
                $request .= " ";
            }
            
        }
    }

    //Preparation de la requete
    $req = $db->prepare($request);
    // Ajout des bindparams (ceux de la clause WHERE)
    if (sizeof($bindparams) > 0){
        for ($i = 0; $i < sizeof($bindparams); $i++){
            if (is_int($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_INT);
            }else if (is_string($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_STR);
            }else if (is_bool($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_BOOL);
            }else {
                return false;
            }
        }
    }
    
    //On execute la requete
    return $req->execute();
}

/**
  * Fonction de simplification, pour les requettes SQL de type UPDATE
  * Cette fonction respecte les bonnes pratiques quant à l'utilisation de PDO, elle limite les failles SQL. Elle est donc a utiliser au maximum.

  * @author Aldric L.
  * @copyright 2020
  * @version 1.0
  * 
  * @param string $from table SQL
  * @param array $set Elements à modifier sous la forme d'un tableau de tableaux représentant chaque égalité
  * @param array|bool $where Condition(s) à effectuer dans la requete SQL, si false alors ignorée
  *
  * @return bool si false, faire un débogage complet, sinon execution normale de la fonction.
  */

  function update($db, $from, $set, $where=false){
    $request = "UPDATE ";

    //Partie FROM
    $request .= $from;
    $request .= " ";

    $bindparams = array();

    //Partie SET
    if (is_array($set)){
        // set = array(array("nom_du_champ", "egalite", "valeur_souhaitée"), ...);
        $request .= "SET ";
        //var_dump($set, $where);
        for ($i = 0; $i < sizeof($set); $i++){
            //var_dump($set[$i]);
            if (is_array($set[$i])){
                //var_dump($set[$i][0]);
                $request .= $set[$i][0];
                $request .= " ";
                $request .= $set[$i][1];
                $request .= " ";
                $request .= "?";
                //S'il s'agit du dernier élèment de la requette
                if ($i+1 == sizeof($set)){
                    $request .= " ";
                }else {
                    $request .= ", ";
                }
                //On ajoute le contenu a tester, pour le passer en bindparam
                array_push($bindparams, $set[$i][2]);
            }
            
        }
    }

    //Partie Where
    if ($where != false && is_array($where)){
        // where = array(array("nom_du_champ", "egalite"), "OR", array("nom_du_champ", "egalite"));
        $request .= "WHERE ";
        //var_dump($where);
        for ($i = 0; $i <= sizeof($where); $i++){
            //Si c'est une condition
            //var_dump(is_array($where[$i]), $where[$i], $where[$i][0]);
            if (array_key_exists($i, $where) && is_array($where[$i])){
                $request .= $where[$i][0];
                $request .= " ";
                $request .= $where[$i][1];
                $request .= " ?";
                //On ajoute le contenu a tester, pour le passer en bindparam
                array_push($bindparams, $where[$i][2]);
                //Si c'est un opérateur
            }else if (array_key_exists($i, $where)){
                $request .= $where[$i];
                $request .= " ";
            }
            
        }
    }
    //var_dump($request);

    //Preparation de la requete
    $req = $db->prepare($request);
    // Ajout des bindparams (ceux de la clause WHERE)
    if (sizeof($bindparams) > 0){
        for ($i = 0; $i < sizeof($bindparams); $i++){
            if (is_int($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_INT);
            }else if (is_string($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_STR);
            }else if (is_bool($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_BOOL);
            }else {
                return false;
            }
        }
    }
   
    //On execute la requete
    return $req->execute();
}

} //END namespace simplifySQL

namespace {
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
  * Différents types de retours :
  * @return 10 : aucun fichier uploadé
  * @return 20 ou 21 : upload_max_filesize : phpini maxsize => Fichier trop important (20 => phpin ; 21 => html)
  * @return 22 : erreur interne inconnue
  * @return 23 : Aucun fichier uploadé
  * @return 24 : problème lié à l'extension du fichier
  * @return 25 : problème d'écriture
  * @return 30 : encore un problème d'écriture, suite au déplacement du fichier (lié quasiment toujours aux droits d'accès : sous linux inscrire 777)
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
}// END namespace