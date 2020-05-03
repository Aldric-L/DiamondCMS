  <div id="fh5co-page-title" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
    <div class="overlay"></div>
    <div class="text">
      <h1>Réglement du Serveur</h1>
    </div>
  </div>
  <div class="content-container">
    <?php echo file_get_contents(ROOT . "config/reglement.ftxt"); ?>
  </div>

