<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>installation/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>installation/sources.css" />
        <title>Installation de Diamond CMS</title>
    </head>

    <body>
    <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.1.js"></script>
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
      <img class="img-responsive" width="500" style="margin: 0;" src="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>/installation/diamondcms.png">
      <h1>Etape 1 : Acceptation des licences</h1>
      <h2>DiamondCMS est un projet gratuit et open-source. <br> Toutefois, pour pouvoir poursuivre l'installation, vous devez accepter ces licences et les conditions d'utilisation.<br></h2>
      <div style="padding-top: 10px;margin-left: 15%; margin-right: 15%;">
        <h3>Conditions générales d'utilisation</h3>
        <div class="embed-responsive embed-responsive-4by3" style="padding-bottom: 200px">
          <iframe src="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>/installation/cgu.md" frameborder="0"></iframe> 
        </div>
        <br>
        <hr width="800">
        <h3>Licence du noyau</h3>
        <div class="embed-responsive embed-responsive-16by9">
          <iframe src="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>/installation/LICENSE.md" frameborder="0"></iframe> 
        </div>
        <br>
        <hr width="800">
        <h3>Licence du thème</h3>
        <div class="embed-responsive embed-responsive-16by9">
          <iframe src="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>/views/themes/default/licence.md" frameborder="0"></iframe> 
        </div>
        <?php foreach (explode(",", DCMS_DEFAULT_ADDONS_INSTALLED) as $a){ ?>
          <br>
          <hr width="800">
          <h3>Licence de l'addon <?= $a; ?></h3><div class="embed-responsive embed-responsive-16by9">
            <iframe src="//<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>addons/<?= $a; ?>/licence.md" frameborder="0"></iframe> 
          </div>
        <?php } ?>
        
      </div>
        </p>
        <br>
        <p><em>En passant à l'étape suivante, vous acceptez les présentes.</em><br><br>
        <button class="btn btn-lg btn-success green" id="next_button" data="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/next.php">Passer à l'étape suivante</button></p>
      </div>
      </center>
        
        
        <!-- LIB JavaScript -->
        <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/font_awesome.js"></script>
        <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/bootstrap.js"></script>
        <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>installation/global.js"></script>
    </body>
</html>