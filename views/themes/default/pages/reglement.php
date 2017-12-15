<?php if ($Serveur_Config['en_reglement']){ ?>
  <div id="fh5co-page-title">
    <div class="overlay"></div>
    <div class="text">
      <h1>Réglement du Serveur</h1>
    </div>
  </div>
  <div class="content-container">
    <?php require ROOT . "config/reglement.ftxt"; ?>
  </div>
<?php }else {
  header('Location:'. $Serveur_Config['protocol'] . '://' .$_SERVER['HTTP_HOST'] . WEBROOT);
} ?>
