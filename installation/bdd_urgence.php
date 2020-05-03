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
      }
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br><br>
      <center>
      <img class="img-responsive" width="850" style="margin: 0;" src="installation/diamondcms.png">
      <h1>Une erreur grave est survenue sur le site que vous visitez !</h1>
      <h2>Ce site, fondé sur DiamondCMS, a été mis en sécurité pour permettre aux administrateurs de palier le problème.<br>
      <br><strong>Merci de réactualiser cette page dans quelque temps.</strong></h2>
      <div style="margin-left: 15%; margin-right: 15%;">
        <br><hr><br>
        <p><strong>Si vous êtes l'administrateur :</strong> Il s'agit d'une erreur liée à la configuration de la base de donnée. <br> Cette dernière a levé l'erreur suivante : <br><?= EXCEPTION; ?></br>.</p>
        <p>Pour modifier les réglages de votre base de données MySQL, rendez-vous dans le dossier config, dans le fichier nommé "bdd.ini";</p>
      </div>
      </center>
	</body>
</html>
