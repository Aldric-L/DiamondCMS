<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
    <div class="overlay"></div>
    <div class="text">
      <h1><?php echo $page_name; ?></h1>
    </div>
  </div>
  <div class="content-container content-editable" data-apilink="<?php echo LINK . "API/"; ?>" data-page="<?php echo $page_name; ?>">
    <?php 
    $content = file_get_contents(ROOT . "config/" .  $file . ".ftxt");
    if (!(isset($_SESSION['editing_mode']) && $_SESSION['editing_mode'])){
        foreach (TEXT_ALIAS as $key => $a){
            $content = str_replace($key, $a, $content);
        }
    }
    echo $content; ?>
  </div>

