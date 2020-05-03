<div id="erreur404content">
<<<<<<< HEAD
  <h1>Oups !</h1>
  <h2>Cette page n'existe pas ou plus !</h2>
  <h3>Pour revenir à l'accueil, <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?><?= WEBROOT ?>">Cliquez-ici !</a></h3>
  <br />
</div>
=======
<h1>Oups !</h1>
<h2>Cette page n'existe pas ou plus !</h2>
<h3>Pour revenir à l'accueil, <a href="http://<?= $Serveur_Config['host']; ?><?= WEBROOT ?>">Cliquez-ici !</a></h3></div>
<style>
  #erreur404content {
  		background-image: url("web_font.png");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
  }
</style>
>>>>>>> f73348d50b56501cae02d84fa1249082fe8b0232
