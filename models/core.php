<?php 
/**
  * Fonction de simplification, pour les requettes SQL de type SELECT
  * Cette fonction respecte les bonnes pratiques quant à l'utilisation de PDO, elle limite les failles SQL. Elle est donc a utiliser au maximum.

  * @author Aldric L.
  * @copyright GougDev 2017-2018
  * @version 1.0
  * 
  * @param boolean $fetch Type de récupération : fetch OU fetchall si $fetch = true alors fetch sinon fetchAll
  * @param string $from table SQL
  * @param array $wanted Elements recupérés par la requete
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
                if ($i != sizeof($wanted)){
                    $request .= ", ";
                }else {
                    $request .= " ";
                }
            }else if (array_key_exists($i, $wanted)){
                $request .= $wanted[$i];
                if (($i+1) < sizeof($wanted)){
                    //var_dump($i, $wanted);
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
            if (is_int($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], PDO::PARAM_INT);
            }else if (is_string($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], PDO::PARAM_STR);
            }else if (is_bool($bindparams[$i])){
                $req->bindParam(($i+1), $bindparams[$i], PDO::PARAM_BOOL);
            }else {
                return false;
            }
        }
    }
    //Ajout des bindparams (ceux de la clause LIMIT)
    if ($limit != false && is_array($limit)){
        $req->bindParam("min", $limit[0], PDO::PARAM_INT);
        $req->bindParam("limite", $limit[1], PDO::PARAM_INT);
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
    return $post;
}