<?php
if ($Serveur_Config['en_vote']){
  if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    define('FORCE_INLINE_ERR', true);
    if (isset($_POST['pseudo'])){
      $hasVote = hasVote($controleur_def->bddConnexion(), $_POST['pseudo']);
      if ($hasVote){
        exit("Vous avez déjà voté aujourd'hui !");
      }else {
        exit();
      }
    }else {
      exit("Merci de vous connecter !");
    }
  }
  if(!empty($_SESSION['pseudo'])){
    $controleur_def->loadModel('vote');
    $hasVote = hasVote($controleur_def->bddConnexion(), $_SESSION['pseudo']);
    if ($hasVote == true){
      $controleur_def->addError(321);
      require('accueil.php');
    }else {
      $addVote = addVote($controleur_def->bddConnexion(), $_SESSION['pseudo']);
      header('Location:' . $Serveur_Config['lien_vote']);
    }
  }else {
    $controleur_def->addError(311);
    require('accueil.php');
  }
}