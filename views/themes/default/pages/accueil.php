<header id="header" class="alt">
  <div class="inner">
    <h1><?= $Serveur_Config['Serveur_name']; ?></h1>
    <p><?= $Serveur_Config['desc']; ?></p>
  </div>
<!--  <img alt="Logo du site" src="http://<?= $_SERVER['HTTP_HOST']; ?><?= WEBROOT; ?>views/uploads/<?= $Serveur_Config['logo'] ?>">
<div id="JSONAPI">
  <p>Etat du serveur :</p>
</div>-->
</header>
<div id="news">
  <div class="container">
    <!--<div class="row">
      <center><h2 class="text-danger">Dernières news sur le serveur :</h2><center>
    </div>-->
    <br /><br />
    <div class="row">
      <div class="col-xs-6 col-sm-4">
          <p class="text-center"><img width= 340px height= 161px src="http://<?php echo $Serveur_Config['host'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $Serveur_Config['img_1'];?>" alt="<?php echo $Serveur_Config['titre_img_1'];?>"></p>
          <p class="news"><?php echo $Serveur_Config['titre_img_1'];?></p>
          <p id="bree-serif" class="text-center"><a href="<?php echo $Serveur_Config['lien_img_1'];?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
      </div>
      <div class="col-xs-6 col-sm-4">
          <p class="text-center"><img width= 340px height= 161px src="http://<?php echo $Serveur_Config['host'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $Serveur_Config['img_2'];?>" alt="<?php echo $Serveur_Config['titre_img_2'];?>"></p>
          <p class="news"><?php echo $Serveur_Config['titre_img_2'];?></p>
          <p id="bree-serif" class="text-center"><a href="<?php echo $Serveur_Config['lien_img_2'];?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
      </div>
      <div class="col-xs-6 col-sm-4">
          <p class="text-center"><img width= 340px height= 161px src="http://<?php echo $Serveur_Config['host'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $Serveur_Config['img_3'];?>" alt="<?php echo $Serveur_Config['titre_img_3'];?>"></p>
          <p class="news"><?php echo $Serveur_Config['titre_img_3'];?></p>
          <p id="bree-serif" class="text-center"><a class="align-right" href="<?php echo $Serveur_Config['lien_img_3'];?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
      </div>
    </div>
  </div>
  <br /><br />
</div>
