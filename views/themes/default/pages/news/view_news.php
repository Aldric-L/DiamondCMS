<?php global $news; ?>
<div id="fh5co-page-title">
  <div class="overlay"></div>
  <div class="text">
    <h1><a class="no" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT; ?>news/">News du serveur</a> - <?php echo $news['name']; ?> par <?php echo $news['user']; ?> le <?php echo $news['date']; ?></h1>
  </div>
</div>
<style>
.no {
    color: #197d62;
    text-decoration: none;
}
</Style>
<div class="content-container">
    <h1><?php echo $news['name']; ?></h1>
    <p>Par <?php echo $news['user']; ?> le <?php echo $news['date']; ?></p>
    <br />
    <p><?php echo $news['content_new']; ?></p>
</div>