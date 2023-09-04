<?php 
/**
 * Modèle Forum : fonctions usuelles pour charger le forum
 * Ces fonctions sont des alias SQL très simples pour gagner du temps
 * et surtout pour assurer la rétrocompatibilité avec les très vieilles versions du CMS
 */

/**
 * getPosts - Fonction alias SQL pour recupérer les derniers posts
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param int $id_scat : id de la sous catégorie
 * @param int $min : rang du premier item à selectionner
 * @param int $limit : nombre d'élements à sélectionner
 * @return array|bool
 */
function getPosts(\PDO $db, int $id_scat, int $min, int $limite) {
    return simplifySQL\select($db, false, "d_forum", array("id, titre_post, user, resolu, content_post, id_scat, nb_rep, date_post", array("date_post", "%d/%m/%Y", "date_p"), "last_edit", "last_editer", array("last_edit", "%d/%m/%Y à %h:%i:%s", "last_edit_date")), array(array("id_scat", "=", $id_scat)), "id", true, array($min, $limite));
}

/**
 * getNPosts - Fonction alias SQL pour recupérer le nombre de posts
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param int $id_scat : id de la sous catégorie
 * @return int
 */
function getNPosts(\PDO $db, int $id_scat) : int {
    return (is_array($req= simplifySQL\select($db, false, "d_forum", array("id"), array(array("id_scat", "=", $id_scat)), "id", true))) ? sizeof($req) : 0;
}

/**
 * getPost - Fonction alias SQL pour recupérer un post unique
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param int $id_post : id du post
 * @return array|bool
 */
function getPost(\PDO $db, int $id_post) {
    return simplifySQL\select($db, true, "d_forum", array("id, titre_post, user, resolu, content_post, id_scat, nb_rep, date_post", "last_edit", "last_editer", array("last_edit", "%d/%m/%Y à %h:%i:%s", "last_edit_date"), array("date_post", "%d/%m/%Y", "date_p")), array(array("id", "=", $id_post)), "id");
}

/**
 * getPost - Fonction alias SQL pour recupérer les commentaires par post
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param int $id_post : id du post
 * @return array|bool
 */
function getComs(\PDO $db, int $id_post) {
    return simplifySQL\select($db, false, "d_forum_com", array("id, content_com, user, date_comment, last_edit, last_editer", array("last_edit", "%d/%m/%Y à %h:%i:%s", "last_edit_date"), array("date_comment", "%d/%m/%Y", "date_com")), array(array("id_post", "=", $id_post)), "id");
}

/**
 * is_solved - Fonction alias SQL pour connaitre l'état du sujet (résolu)
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param int $id_post : id du post
 * @return bool
 */
function is_solved(\PDO $db, int $id_post) : bool {
    return (is_array($rep = simplifySQL\select($db, true, "d_forum", "resolu", array(array("id", "=", $id_post)))) && isset($rep['resolu']) && $rep['resolu'] == 1) ? true : false;
}

/**
 * getCategorie - Fonction alias SQL pour recupérer une catégorie
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param int $id : id de la catégorie
 * @return array|bool
 */
function getCategorie(\PDO $db, int $id) {
    return simplifySQL\select($db, true, "d_forum_cat", array("id", "titre"), array(array("id", "=", $id)), "id");
}

/**
 * getCategorieByName - Fonction alias SQL pour recupérer une catégorie
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param string $name : nom de la catégorie
 * @return array|bool
 */
function getCategorieByName(\PDO $db, string $name) {
    return simplifySQL\select($db, true, "d_forum_cat", array("id", "titre"), array(array("titre", "=", $name)), "id");
}

/**
 * getSousCategorieByName - Fonction alias SQL pour recupérer une sous-catégorie
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param string $name : nom de la sous-catégorie
 * @return array|bool
 */
function getSousCategorieByName(\PDO $db, string $name) {
    return simplifySQL\select($db, true, "d_forum_sous_cat", "*", array(array("titre", "=", $name)), "id");
}

/**
 * getSousCategorie - Fonction alias SQL pour recupérer une sous-catégorie
 * @author Aldric.L
 * @copyright Copyright 2016-2017-2023 Aldric L.
 * @param PDO $db : instance PDO 
 * @param int $id : id de la sous-catégorie
 * @return array|bool
 */
function getSousCategorie(\PDO $db, int $id) {
    return simplifySQL\select($db, true, "d_forum_sous_cat", "*", array(array("id", "=", $id)), "id");
}
  