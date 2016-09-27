<?php
$controleur_def->loadModel('vote');
$hasVote = hasVote($controleur_def->bddConnexion(), "Goug3");
if ($hasVote == true){
  $erreur_vote = "Deja voter";
  require('accueil.php');
}else {
  $addVote = addVote($controleur_def->bddConnexion(), "Goug3");
  header('Location:' . $Serveur_Config['lien_vote']);
}
