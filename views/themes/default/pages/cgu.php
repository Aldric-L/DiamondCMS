<div id="fh5co-page-title" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>CGU / CGV</h1>
  </div>
</div>
<div class="content-container">
  <h1>Conditions Génerales d'utilisation et de vente :</h1>
  <?php echo file_get_contents(ROOT . "config/cgu.ftxt"); ?>
</div>
