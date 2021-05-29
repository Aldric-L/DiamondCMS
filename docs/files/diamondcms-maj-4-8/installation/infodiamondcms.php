<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo LINK;?>installation/bootstrap.css"/>
		    <link rel="stylesheet" type="text/css" href="<?php echo LINK;?>installation/sources.css" />
        <title>Installation de Diamond CMS</title>
    </head>

    <body>
    <script src="//<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>js/jquery-3.1.1.js"></script>
    <style>
      h1, h2{
        text-align: center;
      }
      h1{
        font-size: 3em;
        color: #197d62;
      }
      h2{
        font-family: "Raleway-Light";
        font-size: 1.5em;
      }
      p{
        font-family: "Raleway-Light";
      }
    </style>
      <br>
      <center><br /><br /><br />
      <img class="img-responsive" width="750" style="margin: 0;" src="<?php echo LINK;?>/installation/diamondcms.png">
      <h1>Le bon fonctionnement de ce site <br /> est fièrement assuré par DiamondCMS !</h1>
      <h2>Version <?= DCMS_VERSION; ?>. Ce CMS est 100% gratuit et disponible <a href="https://aldric-l.github.io/DiamondCMS/">ici</a> !<br><br>
      </center>
        
        
        <!-- LIB JavaScript -->
        <script src="<?= LINK; ?>js/font_awesome.js"></script>
        <script src="<?= LINK; ?>js/bootstrap.js"></script>
        <script src="<?= LINK; ?>installation/global.js"></script>
    </body>
</html>