<?php
/**
 * decrypte_cookie - Fonction pour savoir si le cookie est vrai
 * @author Aldric.L
 * @copyright Copyright 2016-2017 2020 Aldric L.
 * @return void
 * @access public
 */
 function decrypte_cookie($db, $cookie){
   //$cookie designe le contenu du cookie de connexion qui contient le pseudo de l'utilisateur hashé en SHA1 avec, devant, un salt
   if (isset($cookie) && !empty($cookie)){
     //On récupère tous les membres de la bdd
     $membres = simplifySQL\select($db, false, 'd_membre', 'pseudo, salt');
     //var_dump($membres);
     //Si on trouve bien des membres
     if (!empty($membres)){
       //On parcours le tableau
       foreach ($membres as $key => $membre) {
         //Si on trouve un pseudo dans le tableau égal à celui du cookie
         //var_dump(sha1($membres[$key]['pseudo']), $cookie, $membres[$key]['pseudo']);
         if(!empty($membres[$key]['salt'])){
            //var_dump(sha1($membres[$key]['salt'] + '_' + $membres[$key]['pseudo']), $cookie);
            if(sha1($membres[$key]['salt'] . '_' . $membres[$key]['pseudo']) == $cookie){
              //On définit la session
              $_SESSION['pseudo'] = $membres[$key]['pseudo'];
            }
         }else if (sha1($membres[$key]['pseudo']) == $cookie){
          $_SESSION['pseudo'] = $membres[$key]['pseudo'];
         }
       }
     }else {
       return false;
     }
   }
 }
