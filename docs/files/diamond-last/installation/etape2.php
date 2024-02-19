<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href=" <?= LINK; ?>installation/assets/css/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href=" <?= LINK; ?>installation/assets/css/sources.css" />
        <title>Installation de Diamond CMS</title>
    </head>

    <body>
    <script src="<?= LINK; ?>installation/assets/js/jquery-3.1.1.js"></script>
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
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br>
      <center>
      <img class="img-responsive" width="500" style="margin: 0;" src=" <?= LINK; ?>/installation/assets/img/diamondcms.png">
      <h1>Etape 2 : Configuration de la Base de donnée</h1>
      <h2>DiamondCMS nécessite une base de donnée MySQL pour fonctionner.</h2>
      <div style="padding-top: 10px;margin-left: 25%; margin-right: 25%;">
      <form method="post">
          <div class="form-group row">
            <label for="host" class="col-sm-2 col-form-label">Host :</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="host">
            </div>
          </div>
          <div class="form-group row">
            <label for="db" class="col-sm-2 col-form-label">Nom de la base de donnée :</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="db">
            </div>
          </div>
          <div class="form-group row">
            <label for="usr" class="col-sm-2 col-form-label">Utilisateur :</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="usr">
            </div>
          </div>
          <div class="form-group row">
            <label for="psw" class="col-sm-2 col-form-label">Mot de passe :</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="psw">
            </div>
          </div>
          <div class="form-group row">
            <label for="port" class="col-sm-2 col-form-label">Port :</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="port" value="3306">
            </div>
          </div>
        <p class="text-right">
        <button type="button" class="testbdd btn btn-warning" data-link="<?= LINK; ?>installation/bdd_test.php">Tester</button>
        <button type="button" id="installbdd" disabled class="installbdd btn btn-danger" data-link="<?= LINK; ?>installation/bdd_test.php">Installer la Base de données</button></p>
        </form>
        
      </div>
        </p>
        <br>
        <p>
        <button class="btn btn-lg btn-success green" id="next_button-2" disabled 
        data-link="<?= LINK; ?>installation/bdd_test.php"
        data="<?= LINK; ?>installation/next.php">Passer à l'étape suivante</button></p>
      </div>
      </center>
        
        
        <!-- LIB JavaScript -->
        <script src="<?= LINK; ?>installation/assets/js/font_awesome.js"></script>
        <script src="<?= LINK; ?>installation/assets/js/bootstrap.js"></script>
        <script src="<?= LINK; ?>installation/assets/js/etape2.js"></script>
        <script src="<?= LINK; ?>installation/assets/js/global.js"></script>
    </body>
</html>