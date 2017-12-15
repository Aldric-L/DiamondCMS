<?php
/**
 * decrypte_cookie - Fonction pour savoir si le cookie est vrai
 * @author Aldric.L
 * @copyright Copyright 2016-2017 Aldric L.
 * @return void
 * @access public
 */
 function decrypte_cookie($db, $cookie){
   //$cookie designe le cookie de connexion qui contient le pseudo de l'utilisateur hasché en SHA1
   if (isset($cookie) && !empty($cookie)){
     //On appelle la foncion getMembreCrypte
     $membres = getMembreCrypte($db, $cookie);
     //Si on trouve bien un membre
     if (!empty($membres)){
       //On parcours le tableau
       foreach ($membres as $key => $membre) {
         //Si on trouve un pseudo dans le tableau égal à celui du cookie
         if(sha1($membres[$key]['pseudo']) == $cookie){
           //On définis la session
           $_SESSION['pseudo'] = $membres[$key]['pseudo'];
         }
       }
     }else {
       return false;
     }
   }
 }

 /**
  * getMembreCrypte - Fonction pour savoir si le pseudo pssé en paramètre existe sur la BDD
  * @author Aldric.L
  * @copyright Copyright 2016-2017 Aldric L.
  * @return array
  * @access private
  */
 function getMembreCrypte($db, $pseudo){
   //On récupère tous les membres
   $req = $db->query("SELECT pseudo FROM d_membre");
   $rep = $req->fetchAll();
   //On ferme la requete
   $req->closeCursor();

   //On retourne le pseudo
   return $rep;
 }
