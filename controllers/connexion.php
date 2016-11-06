<?php
//On charge le model
$controleur_def->loadModel('comptes/connexion');

//================ CONNEXION ================
//On fait toutes les vérifications nécessaires à la connexion
if (!empty($_POST)){
  if (!empty($_POST['pseudo_connexion'])){
    if (!empty($_POST['mp_connexion'])){
        $is_account = isAccount($controleur_def->bddConnexion(), htmlspecialchars($_POST['pseudo_connexion']), htmlspecialchars($_POST['mp_connexion']));
        //Si on trouve un compte
        if ($is_account == 1){
          $_SESSION['pseudo'] = htmlspecialchars($_POST['pseudo_connexion']);
          if (isset($_POST['souvenir'])){
            setcookie('pseudo', sha1(htmlspecialchars($_POST['pseudo_connexion'])), time() + 365*24*3600, null, null, false, true);
          }
        }else {
          $erreur_connexion = "Erreur, aucun compte ne corresponds ! Verifiez vos identifiants de connexion.";
        }
    }else {
      $erreur_connexion = "Vous devez préciser un mot de passe !";
    }
  }else {
    $erreur_connexion = "Vous devez préciser un pseudo !";
  }
}

require('accueil.php');
