<?php
if (!isset($_SESSION['pseudo']) || empty($_SESSION['pseudo'])){
  //On charge le model
  $controleur_def->loadModel('comptes/connexion');
  
  if (@file_exists(ROOT . "installation/blocked.dcms")){
    define('DIAMOND_BLOCKED', true);
    require_once(ROOT . 'installation/infodiamondcms.php'); die;
  }

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
            if ($Serveur_Config['mtnc'] == "true"){
              header('Location: '. LINK);
            }
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
              if ($Serveur_Config['mtnc'] == "true"){
                header('Location: '. LINK);
              }
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
              header('Location: '. LINK . $_POST['page']);
            }else {
              header('Location: '. LINK);
            }
          }else {
            $controleur_def->addError(332);
            if ($Serveur_Config['mtnc'] == "true"){
              header('Location: '. LINK);
            }
          }
      }else {
        $controleur_def->addError("331h");
        if ($Serveur_Config['mtnc'] == "true"){
          header('Location: '. LINK);
        }
      }
    }else {
      $controleur_def->addError("331g");
      if ($Serveur_Config['mtnc'] == "true"){
        header('Location: '. LINK);
      }
    }
  }

  require('accueil.php');
}else {
  header('Location: '. LINK);
}
