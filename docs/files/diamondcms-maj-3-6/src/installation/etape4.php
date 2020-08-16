<?php
require_once(ROOT . 'models/comptes/inscription.php'); 
require_once(ROOT . 'models/DiamondCore/db.class.php');
require_once(ROOT . 'models/bdd_connexion.php');
global $Serveur_Config;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="<?= LINK; ?>installation/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href="<?= LINK; ?>installation/sources.css" />
        <title>Installation de Diamond CMS</title>
    </head>

    <body>
    <script src="<?= LINK; ?>js/jquery-3.1.1.js"></script>
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
      <img class="img-responsive" width="500" style="margin: 0;" src="<?= LINK; ?>/installation/diamondcms.png">
      <h1>Etape 4 : Configuration principale</h1>
      <h2>Afin d'initialiser votre site, DiamondCMS a besoin de premières informations, modifiables par la suite.</h2>
      <div style="padding-top: 10px;margin-left: 36%; margin-right: 36%;">
      <form method="post" id="conf">
        <div class="form-group">
            <label for="Serveur_name" class="col-form-label">Nom du serveur (ou de votre entreprise) :</label>
             <input class="form-control" type="text" name="Serveur_name" id="Serveur_name" value="<?= $Serveur_Config['Serveur_name']; ?>">
        </div>
        <div class="form-group">
            <label for="protocol" class="col-form-label">Protocol web (http ou https)</label>
            <input class="form-control" type="text" name="protocol" id="protocol" value="<?= $Serveur_Config['protocol']; ?>">
        </div>
        <div class="form-group">
            <label for="desc" class="col-form-label">Description de votre serveur (ou collectif)</label>
            <input class="form-control" type="text" name="desc" id="desc" value="<?= $Serveur_Config['desc']; ?>">
        </div>
        <div class="form-group">
            <label for="about_footer" class="col-form-label">A propos de vous : (texte du footer)</label>
            <input class="form-control" type="text" name="about_footer" id="about_footer" value="<?= $Serveur_Config['about_footer']; ?>">
        </div>
      </form>
      </div>
        <br>
        <p>Le reste de la configuration sera à réaliser dans le panel d'administration (notamment la configuration du lien avec les serveurs).<br><em>Vous n'avez pas à modifier vous même les fichiers de configuration du CMS.</em></p>
        <button class="btn btn-lg btn-success green" id="last_button" 
        data-first="<?= LINK; ?>installation/save_conf.php"
        data="<?= LINK; ?>installation/next.php"
        >Accèder à votre site internet !</button></p>
      </div>
      </center>
        
        
        <!-- LIB JavaScript -->
        <script src="<?= LINK; ?>js/font_awesome.js"></script>
        <script src="<?= LINK; ?>js/bootstrap.js"></script>
        <script src="<?= LINK; ?>installation/etape4.js"></script>
        <script src="<?= LINK; ?>installation/global.js"></script>
    </body>
</html>