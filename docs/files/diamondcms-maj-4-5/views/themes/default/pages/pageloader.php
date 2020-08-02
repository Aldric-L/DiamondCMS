<div id="fh5co-page-title" style="background-image: url(<?php echo LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
    <div class="overlay"></div>
    <div class="text">
      <h1><?= $page_name; ?></h1>
    </div>
  </div>
  <div class="content-container">
    <?php echo file_get_contents(ROOT . "config/" .  $file . ".ftxt"); ?>
  </div>

