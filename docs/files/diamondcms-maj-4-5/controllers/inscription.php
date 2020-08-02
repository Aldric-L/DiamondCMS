<?php
if (!isset($_SESSION['pseudo']) || empty($_SESSION['pseudo'])){
  //On charge le model
  $controleur_def->loadModel('comptes/inscription');
  //================ INSCRIPTION ================
  //On fait toutes les vérifications nécessaires à l'inscription
  if (!empty($_POST)){
    if (!empty($_POST['pseudo_inscription'])){
      if (strpos($_POST['pseudo_inscription'], " ") == false){
        if (!empty($_POST['email_inscription'])){
          if (!empty($_POST['mp_inscription'])){
            if (!empty($_POST['mp2_inscription'])){
              //print_r($_POST);
              if (isset($_POST['news'])){
                $inscription = addMembre($controleur_def->bddConnexion(), htmlspecialchars($_POST['pseudo_inscription']), htmlspecialchars($_POST['email_inscription']), 1, htmlspecialchars($_POST['mp_inscription']), htmlspecialchars($_POST['mp2_inscription']));
              }else {
                $inscription = addMembre($controleur_def->bddConnexion(), htmlspecialchars($_POST['pseudo_inscription']), htmlspecialchars($_POST['email_inscription']), 0, htmlspecialchars($_POST['mp_inscription']), htmlspecialchars($_POST['mp2_inscription']));
              }
              if ($inscription == 1){
                $controleur_def->addError("331f");
              }elseif ($inscription == 2) {
                $controleur_def->addError("331e");
              }elseif ($inscription == 3) {
                $controleur_def->addError("333");
              }else {
                $_SESSION['pseudo'] = htmlspecialchars($_POST['pseudo_inscription']);
  
                if (!empty($_POST['page'])){
                  header('Location: '. LINK . $_POST['page']);
                  exit();
                }
              }
            }else {
              $controleur_def->addError("331d");
            }
          }else {
            $controleur_def->addError("331c");
          }
        }else {
          $controleur_def->addError("331b");
        }
      }else {
        $controleur_def->addError("331i");
      }
    }else {
      $controleur_def->addError("331a");
    }
  }

  require('accueil.php');
}else {
  header('Location: '. LINK);
}
