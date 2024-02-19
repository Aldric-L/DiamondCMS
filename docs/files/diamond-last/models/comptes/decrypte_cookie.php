<?php
/**
 * decrypte_cookie - Fonction pour savoir si le cookie est vrai
 * @author Aldric.L
 * @copyright Copyright 2016-2017 2020 2022 Aldric L.
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
              $pseudo = $membres[$key]['pseudo']; break;
            }
         }else if (sha1($membres[$key]['pseudo']) == $cookie){
          $pseudo = $membres[$key]['pseudo']; break;
         }
       }
      if (!isset($pseudo))
       return false;

      try {
        $user = new User ($pseudo, $db);
      }catch (Throwable $e){
        setcookie('pseudo', "", time(), WEBROOT, $_SERVER['HTTP_HOST'], false, true); return false;
      }

      if(isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user']->getId() != $user->getId()){
        setcookie('pseudo', "", time(), WEBROOT, $_SERVER['HTTP_HOST'], false, true); return false;
      }

      if(isset($_SESSION['pseudo']) && !empty($_SESSION['pseudo']) && $_SESSION['pseudo'] != $user->getPseudo()){
        setcookie('pseudo', "", time(), WEBROOT, $_SERVER['HTTP_HOST'], false, true); return false;
      }

      // On enregistre la dernière connexion pour faire des stats
      // et on prend aussi l'IP pour le ban ip
      $mod = simplifySQL\update($db, "d_membre", array(
          array("date_last_connect", "=", date("Y-m-d H:i:s")),
          array("nb_connections", "=", $user->getNbConnections()+1 ),
          array("date_lc_timestamp", "=", time()),
          array("ip", "=", $_SERVER['REMOTE_ADDR'])), array(array("id", "=", $user->getId())));

      if ($user->isBanned()){
        setcookie('pseudo', "", time(), WEBROOT, $_SERVER['HTTP_HOST'], false, true); return false;
      }
      
      $_SESSION['user'] = $user;
      $_SESSION['pseudo'] = $pseudo; //par soucis de rétrocompatibilité
     }else {
       return false;
     }
   }
 }
