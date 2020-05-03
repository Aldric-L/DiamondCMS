<?php
<<<<<<< HEAD
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
                  header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT . $_POST['page']);
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
  header('Location: '. $Serveur_Config['protocol'] . '://'. $_SERVER['HTTP_HOST'] . WEBROOT);
}
=======
//On charge le controlleur
$controleur_def = new Controleur($Serveur_Config);

//On charge le model pour le vote...
$controleur_def->loadModel('vote');

//On récupère la connexion à la base de donnée pour la fonction de récupération des meilleurs voteurs
$voteurs = bestVotes($controleur_def->bddConnexion());

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
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
