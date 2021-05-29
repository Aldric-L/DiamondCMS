<?php 
if (!empty($_GET['p'])){
  header('Location: http://' .  $_SERVER['HTTP_HOST'] . WEBROOT);
} ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="description" content="Sécurité DIAMONDCMS" />
        <link rel="stylesheet" type="text/css" href="installation/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="installation/sources.css" />
        <title>Sécurité DiamondCMS</title>
    </head>

    <body>
    <style>
      h1, h2{
        text-align: center;
      }
      h1{
        font-size: 3em;
      }
      h2{
        font-family: "Raleway-Light";
        font-size: 1.5em;
        line-height: 1.5;
      }
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br><br>
      <center>
      <img class="img-responsive" width="850" style="margin: 0;" src="installation/diamondcms.png">
      <h1>Une erreur grave est survenue sur la page que vous visitez !</h1>
      <h2>Ce site, fondé sur DiamondCMS, a été mis en sécurité pour permettre aux administrateurs de palier le problème.<br>
      <strong>Merci de réactualiser cette page dans quelque temps.</strong><br>
      <em style="font-size:0.8em;">Vous pouvez aussi essayer de poursuivre votre navigation si le problème relevé ne concerne que cette page.</em></h2>
      <div style="margin-left: 15%; margin-right: 15%;">
        <br><hr><br>
        <p><strong>Si vous êtes l'administrateur :</strong> Il s'agit d'une erreur interne critique. Vous devez vous rendre sur <a href="https://github.com/Aldric-L/DiamondCMS/wiki">la documentation de DiamondCMS</a> si vous ne comprenez pas l'erreur qui suit :<br>
        <br><strong>Type : <?= $type; ?></strong> - <?= $errstr; ?> (Code d'erreur PHP: <?= $errno; ?>)<br>
        <em>Erreur surevenue à la ligne <?= $errline; ?> du fichier <?= $errfile; ?>.</em>
        </p>
      </div>
      </center>
	</body>
</html>
