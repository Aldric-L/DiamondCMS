<div id="erreur404content">
  <h1>Oups !</h1>
  <h2>Cette page n'existe pas ou plus !</h2>
  <h3>Pour revenir à l'accueil, <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?><?= WEBROOT ?>">Cliquez-ici !</a></h3>
  <br />
</div>
<style>
  #erreur404content {
  		background-image: url("web_font.png");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
  }
</style>
