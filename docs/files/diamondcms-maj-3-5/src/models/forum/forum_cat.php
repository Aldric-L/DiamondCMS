<?php
/**
 *
 * Ces fonctions sont quasiment toutes dépréciées :
 * @deprecated  Il convient d'utiliser les fonctions de simplification du fichier core.php (select, insert, ..) pour dialoguer avec la BDD
 * Elles seront donc supprimées d'ici une prochaine mise à jour. (Dernière édition - avril 2020)
 */

function getCategorie($db, $id){
  $req = $db->prepare('SELECT id, titre FROM d_forum_cat WHERE id='. $id . ' ORDER BY id');

  //On execute la requete
  $req->execute();
  //On récupère tout
  $cats = $req->fetch();
  //On ferme la requete
  $req->closeCursor();

  return $cats;
}
function getCategorieByName($db, $name){
  $req = $db->prepare('SELECT id, titre FROM d_forum_cat WHERE titre = "'. $name_cat . '" ORDER BY id');

  //On execute la requete
  $req->execute();
  //On récupère tout
  $cats = $req->fetch();
  //On ferme la requete
  $req->closeCursor();

  return $cats;
}
function getSousCategorieByName($db, $name_cat){
  $req = $db->prepare('SELECT * FROM d_forum_sous_cat WHERE titre = "'. $name_cat . '" ORDER BY id');

  //On execute la requete
  $req->execute();
  //On récupère tout
  $cats = $req->fetch();
  //On ferme la requete
  $req->closeCursor();

  return $cats;
}
function getSousCategorie($db, $id_cat){
  $req = $db->prepare('SELECT * FROM d_forum_sous_cat WHERE id=' . $id_cat . ' ORDER BY id');
  //On execute la requete
  $req->execute();
  //On récupère tout
  $cats = $req->fetchAll();
  //On ferme la requete
  $req->closeCursor();

  return $cats;
}
