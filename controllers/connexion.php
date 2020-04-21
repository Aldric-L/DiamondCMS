<?php
if (!isset($_SESSION['pseudo']) || empty($_SESSION['pseudo'])){
  //On charge le model
  $controleur_def->loadModel('comptes/connexion');

  //================ CONNEXION ================
  //On fait toutes les vérifications nécessaires à la connexion
  if (!empty($_POST)){
    if (!empty($_POST['pseudo_connexion'])){
      if (!empty($_POST['mp_connexion'])){
          //On fait une pause pour ralentir les possibles hackers
          sleep(1);
          $salt = simplifySQL\select($controleur_def->bddConnexion(), true, "d_membre", "salt", array(array("pseudo", "=", $_POST['pseudo_connexion'])));
          if ($salt == false){
            $controleur_def->addError(332);
            require('accueil.php');
            die;
          }
          $is_account = isAccount($controleur_def->bddConnexion(), $Serveur_Config, htmlspecialchars($_POST['pseudo_connexion']), htmlspecialchars($_POST['mp_connexion']), $salt['salt']);
          //Si on trouve un compte
          if ($is_account){
            $is_ban = isBan($controleur_def->bddConnexion(), htmlspecialchars($_POST['pseudo_connexion']));
            if ($is_ban != false){
              $ban = true;
              $r_ban = isBan($controleur_def->bddConnexion(), htmlspecialchars($_POST['pseudo_connexion']));
              require('accueil.php');
              die;
            }
            $ban = false;
            $_SESSION['pseudo'] = htmlspecialchars($_POST['pseudo_connexion']);
            $erreur_connexion = "";
            if (isset($_POST['souvenir'])){
              //On place un cookie dans lequel on inscrit le salt, un underscore, et le pseudo de connexion, le tout est hashé pour protéger le système
              if (!empty($salt['salt'])){
                setcookie('pseudo', sha1($salt['salt'] + '_' + htmlspecialchars($_POST['pseudo_connexion'])), time() + 15*24*3600, WEBROOT, $_SERVER['HTTP_HOST'], false, true);
              }else {
                setcookie('pseudo', sha1(htmlspecialchars($_POST['pseudo_connexion'])), time() + 15*24*3600, WEBROOT, $_SERVER['HTTP_HOST'], false, true);
              }
              $erreur_connexion = "";
            }
            if (!empty($_POST['page'])){
              header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . $_POST['page']);
            }else {
              header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT);
            }
          }else {
            $controleur_def->addError(332);
          }
      }else {
        $controleur_def->addError("331h");
      }
    }else {
      $controleur_def->addError("331g");
    }
  }

  require('accueil.php');
}else {
  header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT);
}
