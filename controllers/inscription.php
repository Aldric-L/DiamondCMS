<?php
//On charge le controlleur
$controleur_def = new Controleur($Serveur_Config);
//On charge le model
$controleur_def->loadModel('inscription');
//================ INSCRIPTION ================
//On fait toutes les vérifications nécessaires à l'inscription
if (!empty($_POST)){
  if (!empty($_POST['pseudo_inscription'])){
    if (!empty($_POST['email_inscription'])){
      if (!empty($_POST['mp_inscription'])){
        if (!empty($_POST['mp2_inscription'])){
          if ($_POST['news'] == "on"){
            $inscription = addMembre($controleur_def->bddConnexion(), $_POST['pseudo_inscription'], $_POST['email_inscription'], 1, $_POST['mp_inscription'], $_POST['mp2_inscription']);
          }else {
            $inscription = addMembre($controleur_def->bddConnexion(), $_POST['pseudo_inscription'], $_POST['email_inscription'], 0, $_POST['mp_inscription'], $_POST['mp2_inscription']);
          }
          if ($inscription == 1){
            $erreur = "Vous avez déjà un compte, connectez-vous !";
          }elseif ($inscription == 2) {
            $erreur = "Votre mot de passe doit faire plus de 6 charactères";
          }elseif ($inscription == 3) {
            $erreur = "Vous devez préciser le deuxième mot de passe !";
          }
        }else {
          $erreur = "Vous devez préciser le deuxième mot de passe !";
        }
      }else {
        $erreur = "Vous devez préciser un mot de passe !";
      }
    }else {
      $erreur = "Vous devez préciser un email !";
    }
  }else {
    $erreur = "Vous devez préciser un pseudo !";
  }
}
if (!empty($erreur)){
  echo $erreur;
}

//================ CONNEXION ================
//On fait toutes les vérifications nécessaires à l'inscription
if (!empty($_POST)){
  if (!empty($_POST['pseudo_connexion'])){
    if (!empty($_POST['mp_connexion'])){
      print_r($_POST);
        echo "Validation des post connexion";
        //$inscription = addMembre($controleur_def->bddConnexion(), $_POST['pseudo_connexion'], $_POST['email_connexion'], $_POST['news_connexion'], $_POST['mp_connexion'], $_POST['mp2_connexion']);
    }else {
      $erreur = "Vous devez préciser un mot de passe !";
    }
  }else {
    $erreur = "Vous devez préciser un pseudo !";
  }
}
//ob_start();
$controleur_def->loadView('pages/inscription', "inscription");
//$test = ob_get_clean();
$css = $controleur_def->css . '.css';
//echo $css;
//echo $test;
