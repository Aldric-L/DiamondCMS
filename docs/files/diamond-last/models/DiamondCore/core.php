<?php 
namespace simplifySQL {

/**
  * Fonction de simplification, pour les requettes SQL de type SELECT
  * Cette fonction respecte les bonnes pratiques quant à l'utilisation de PDO, elle limite les failles SQL. Elle est donc a utiliser au maximum.

  * @author Aldric L.
  * @copyright 2017-2018
  * @version 1.2
  * 
  * @param boolean $fetch Type de récupération : fetch OU fetchall si $fetch = true alors fetch sinon fetchAll
  * @param string $from table SQL
  * @param array $wanted Elements recupérés par la requete (Syntaxe : soit un string "elem1, elem2" soit un array : array("elem1", "elem2", array("date_bdd", "format", "as")))
  * @param array|bool $where Condition(s) à effectuer dans la requete SQL, si false alors ignorée (Syntaxe : $where = array(array("nom_du_champ", "egalite", "valeur"), "OR", array("nom_du_champ", "egalite", "valeur"));) Note: Un passage en mode manuel est possible sur ce champ en indiquant en string la condition
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
    if ($where != false && is_array($where) && !is_string($where)){
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
    }else {
        //si on passe en mode manuel
        if (\is_string($where)){
            $request .= "WHERE ";
            $request .= $where;
            $request .= " ";
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
            }else if (is_null($values[$i][1])){
                $req->bindParam($bindparams[$i][0], $bindparams[$i][1], \PDO::PARAM_NULL);
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
        //On ne récupère, généralement, qu'une seule ligne
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
  * Attention, cette fonction ne fonctionne que s'il y a correspondance parfaite entre les colonnes de $wanted, et les valeurs de $values qui y seront insérées
  * Exemple : $wanted=array("col1","col2") et $values=array("valeur_de_la_col1", "valeur_de_la_col2")
  * 
  * @author Aldric L.
  * @copyright 2020
  * @version 1.0
  * 
  * @param string $into table SQL
  * @param array $wanted nom des colonnes modifiées
  * @param array|bool $values valeurs à insérer
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
            }else if (is_null($values[$i])){
                $req->bindParam($bindparams[$i], $values[$i], \PDO::PARAM_NULL);
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
  * @param array|bool $where Condition(s) à effectuer dans la requete SQL, si false alors ignorée (Syntaxe : $where = array(array("nom_du_champ", "egalite", "valeur"), "OR", array("nom_du_champ", "egalite", "valeur")))
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
            }else if (is_null($values[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_NULL);
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
  * @version 1.1
  * 
  * Cette fonction supporte désormais les champs nulls
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
        $c=0;
        foreach ($set as $i => &$s){
            //var_dump($set[$i]);
            if (is_array($set[$i]) && is_numeric($i)){
                //var_dump($set[$i][0]);
                $request .= $set[$i][0];
                $request .= " ";
                $request .= $set[$i][1];
                $request .= " ";
                if ($set[$i][2] === NULL){
                    $request .= "NULL";
                }else {
                    $request .= "?";
                    //On ajoute le contenu a tester, pour le passer en bindparam
                    array_push($bindparams, $set[$i][2]);
                }
                
                //S'il s'agit du dernier élèment de la requette
                if ($i+1 == sizeof($set)){
                    $request .= " ";
                }else {
                    $request .= ", ";
                }
                
            //Si on veut passer en mode array("nom_du_champ" => "valeur_souhaitée", ...);
            }else if (!is_array($set[$i])){
                $request .= $i;
                $request .= " = ";
                if ($s === NULL){
                    $request .= "NULL";
                }else {
                    $request .= "?";
                    //On ajoute le contenu a tester, pour le passer en bindparam
                    array_push($bindparams, $s);
                }
                
                //S'il s'agit du dernier élèment de la requette
                if ($c+1 == sizeof($set)){
                    $request .= " ";
                }else {
                    $request .= ", ";
                }
                ++$c;
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
                if ($where[$i][2] === NULL){
                    $request .= "NULL";
                }else {
                    $request .= "?";
                    //On ajoute le contenu a tester, pour le passer en bindparam
                    array_push($bindparams, $where[$i][2]);
                }
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
            }else if (is_null($values[$i])){
                $req->bindParam(($i+1), $bindparams[$i], \PDO::PARAM_NULL);
            }else {
                return false;
            }
        }
    }
   
    //On execute la requete
    return $req->execute();
}

} //END namespace simplifySQL
