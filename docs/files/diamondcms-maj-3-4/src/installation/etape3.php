<?php
require_once(ROOT . 'models/comptes/inscription.php'); 
require_once(ROOT . 'models/DiamondCore/init.php');
require_once(ROOT . 'models/users.trait.php');
require_once(ROOT . 'models/user.class.php');
$errors = array();
$done = false;
//On fait toutes les vérifications nécessaires à l'inscription
if (!empty($_POST)){
  if (!empty($_POST['pseudo_inscription'])){
    if (strpos($_POST['pseudo_inscription'], " ") == false){
      if (!empty($_POST['email_inscription'])){
        if (!empty($_POST['mp_inscription'])){
          if (!empty($_POST['mp2_inscription'])){
            $bdd = new BDD(parse_ini_file(ROOT . "config/bdd.ini", true));
            $inscription = addMembre($bdd->getPDO(), htmlspecialchars($_POST['pseudo_inscription']), htmlspecialchars($_POST['email_inscription']), 0, htmlspecialchars($_POST['mp_inscription']), htmlspecialchars($_POST['mp2_inscription']), true);
            if ($inscription == 1){
              array_push($errors,"Les deux mots de passe ne correspondent pas !");
            }elseif ($inscription == 2) {
              array_push($errors,"Votre mot de passe doit faire plus de 6 charactères pour valider votre inscription !");
            }elseif ($inscription == 3) {
              array_push($errors, "Votre pseudo ou votre email ou votre ip sont déjà utilisés dans un autre compte !");
            }else {
              $_SESSION['pseudo'] = htmlspecialchars($_POST['pseudo_inscription']);
              $_SESSION['user'] = new User($_SESSION['pseudo'], $bdd->getPDO());
              $done = true;
            }
          }else {
            array_push($errors,"Vous devez préciser le deuxième mot de passe pour valider votre inscription !");
          }
        }else {
          array_push($errors,"Vous devez préciser un mot de passe pour vous inscrire !");
        }
      }else {
        array_push($errors,"Vous devez préciser une adresse email pour votre inscription !");
      }
    }else {
      array_push($errors,"Les espaces ne sont pas autorisés dans les pseudos !");
    }
  }else {
    array_push($errors,"Vous devez préciser un pseudo pour vous inscrire !");
  }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href=" //<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>installation/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href=" //<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>installation/sources.css" />
        <title>Installation de Diamond CMS</title>
    </head>

    <body>
    <script src=" //<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.1.js"></script>
    <style>
      h1, h2{
        text-align: center;
      }
      h1{
        font-size: 2.5em;
        color: #197d62;
      }
      h3{
        font-size: 1.5em;
        font-family: "BreeSerif";
        color: #197d62;
      }
      h2{
        font-family: "Raleway-Light";
        font-size: 1.2em;
      }
      h5{
        font-family: "Raleway-Light";
        font-size: 1.2em;
      }
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br>
      <center>
      <img class="img-responsive" width="500" style="margin: 0;" src=" //<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>/installation/diamondcms.png">
      <h1>Etape 3 : Création du compte diamond_master</h1>
      <h2>Pour administrer DiamondCMS, vous devez utiliser un compte disposant du rôle diamond_master.</h2>
      <div style="padding-top: 10px;margin-left: 36%; margin-right: 36%;">
      <?php if (!empty($errors)){ ?>
          <h5>Erreur(s) levée(s) par le systeme :
                <?php foreach ($errors as $err) {?>
                 <br><span style="font-weight: bold;"><?php echo $err; ?></span>
                <?php } ?>
          </h5>
        <?php } ?>
        <?php if ($done){ ?>
          <h5>Votre compte a bien été enregistré !</h5>
        <?php } ?>
        <form method="post" action="" id="inscription_form">
          <div class="form-group">
            <label for="pseudo_inscription" class="col-form-label">Votre pseudo :</label>
            <input class="form-control" type="text" name="pseudo_inscription" id="pseudo_inscription" <?php if ($done){ ?> disabled <?php } ?>>
            <small id="pseudoHelp" class="form-text text-muted">Il doit être le même que celui in-game.</small>
          </div>
          <div class="form-group">
            <label for="email_inscription" class="col-form-label">Email</label>
            <input class="form-control" type="email" id="email_inscription" name="email_inscription" <?php if ($done){ ?> disabled <?php } ?>>
            <small id="mpHelp" class="form-text text-muted">Elle ne sera pas divulguée et nous n'en abuserons pas.</small>
          </div>
          <div class="form-group">
            <label for="mp_inscription" class="col-form-label">Mot de passe</label>
            <input class="form-control" type="password" id="mp_inscription" name="mp_inscription" <?php if ($done){ ?> disabled <?php } ?>>
            <small id="mpHelp" class="form-text text-muted">Il doit faire plus de 6 caractères.</small>
          </div>
          <div class="form-group">
            <label for="mp2_inscription" class="col-form-label">Confirmation du mot de passe</label>
            <input class="form-control" type="password" id="mp2_inscription" name="mp2_inscription" <?php if ($done){ ?> disabled <?php } ?>>
            <small id="mp2Help" class="form-text text-muted">Répetez-le.</small>
          </div>
          <button class="btn btn-md btn-success green" type="submit" <?php if ($done){ ?> disabled <?php } ?>>S'inscrire</button>
        </form>
      </div>
        </p>
        <br>
        <p>
        <button class="btn btn-lg btn-success green" id="next_button" 
        data-link=" //<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/bdd_test.php"
        data=" //<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/next.php"
        <?php if (!$done){ ?> disabled <?php } ?>>Passer à l'étape suivante</button></p>
      </div>
      </center>
        
        
        <!-- LIB JavaScript -->
        <script src=" //<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/font_awesome.js"></script>
        <script src=" //<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.js"></script>
        <script src=" //<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/global.js"></script>
    </body>
</html>