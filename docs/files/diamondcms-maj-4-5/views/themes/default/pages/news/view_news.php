<?php global $news; ?>
<div id="fh5co-page-title" style="background-image: url(<?= LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1><a class="no" href="<?php echo LINK; ?>news/">News du serveur</a> - <?php echo $news['name']; ?> par <?php echo $news['user']; ?> le <?php echo $news['date']; ?></h1>
  </div>
</div>
<style>
.no {
    color: #197d62;
    text-decoration: none;
}
</Style>
<div class="content-container">
    <br />
    <?php if ($news['img'] != "noimg") { ?>
      <p class="text-center"><img style="max-width: 800px" class="img-rounded" src="<?php echo LINK;?>views/uploads/img/<?php echo $news['img'];?>" alt="<?php echo $news['name'];?>"></p>
      <br>
    <?php } ?>
    
    <h1><?php echo $news['name']; ?></h1>
    <p>Par <?php echo $news['user']; ?> le <?php echo $news['date']; ?></p>
    <p><?php echo $news['content_new']; ?></p>
</div>