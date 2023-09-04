<?php global $news; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1><a href="<?php echo LINK; ?>news/">News du serveur</a> - <?php echo $news['name']; ?> </h1>
  </div>
</div>
<div class="content-container">
    <br />
    <?php if ($news['img'] != "noimg") { ?>
      <p class="text-center"><img style="max-width: 800px" class="img-rounded" src="<?php echo LINK;?>cloud/img/<?php echo str_replace(".", "/", $news['img']);?>" alt="<?php echo $news['name'];?>"></p>
      <br>
    <?php } ?>
    
    <h1><?php echo $news['name']; ?><small><em> Par <?php echo $news['user']; ?> le <?php echo $news['date']; ?></em></small></h1>
    <p><?php echo $news['content_new']; ?></p>
</div>