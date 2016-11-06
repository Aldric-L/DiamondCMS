<?php
//On charge le model
$controleur_def->loadModel('comptes/inscription');

//================ INSCRIPTION ================
//On fait toutes les vérifications nécessaires à l'inscription
if (!empty($_POST)){
  if (!empty($_POST['pseudo_inscription'])){
    if (!empty($_POST['email_inscription'])){
      if (!empty($_POST['mp_inscription'])){
        if (!empty($_POST['mp2_inscription'])){
          print_r($_POST);
          if (isset($_POST['news'])){
            $inscription = addMembre($controleur_def->bddConnexion(), htmlspecialchars($_POST['pseudo_inscription']), htmlspecialchars($_POST['email_inscription']), 1, htmlspecialchars($_POST['mp_inscription']), htmlspecialchars($_POST['mp2_inscription']));
            $_SESSION['pseudo'] = htmlspecialchars($_POST['pseudo_inscription']);
          }else {
            $inscription = addMembre($controleur_def->bddConnexion(), htmlspecialchars($_POST['pseudo_inscription']), htmlspecialchars($_POST['email_inscription']), 0, htmlspecialchars($_POST['mp_inscription']), htmlspecialchars($_POST['mp2_inscription']));
            $_SESSION['pseudo'] = htmlspecialchars($_POST['pseudo_inscription']);
          }
          if ($inscription == 1){
            $erreur_inscription = "Vous avez déjà un compte, connectez-vous !";
          }elseif ($inscription == 2) {
            $erreur_inscription = "Votre mot de passe doit faire plus de 6 charactères";
          }elseif ($inscription == 3) {
            $erreur_inscription = "Votre pseudo est déja utilisé.";
          }
        }else {
          $erreur_inscription = "Vous devez préciser le deuxième mot de passe !";
        }
      }else {
        $erreur_inscription = "Vous devez préciser un mot de passe !";
      }
    }else {
      $erreur_inscription = "Vous devez préciser une adresse email !";
    }
  }else {
    $erreur_inscription = "Vous devez préciser un pseudo !";
  }
}

require('accueil.php');
