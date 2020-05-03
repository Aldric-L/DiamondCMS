<?php global $content; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)" >
  <div class="overlay"></div>
  <div class="text">
    <h1>Mentions Légales</h1>
  </div>
</div>
<div class="content-container">
    <h1>Mentions légales :</h1>
    <?php echo file_get_contents(ROOT . "config/m-legal.ftxt"); ?>
</div>  