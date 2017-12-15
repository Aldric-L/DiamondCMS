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
        <center>
          <p class="text-center"><img class="img-responsive img-rounded" src="http://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $Serveur_Config['img_1'];?>" alt="<?php echo $Serveur_Config['titre_img_1'];?>"></p>
          <p class="news"><?php echo $Serveur_Config['titre_img_1'];?></p>
          <p class="text-center bree-serif"><a href="<?php echo $Serveur_Config['lien_img_1'];?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
        <center>
      </div>
      <div class="col-xs-6 col-sm-4">
          <p class="text-center"><img class="img-responsive img-rounded" src="http://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $Serveur_Config['img_2'];?>" alt="<?php echo $Serveur_Config['titre_img_2'];?>"></p>
          <p class="news"><?php echo $Serveur_Config['titre_img_2'];?></p>
          <p class="text-center bree-serif"><a href="<?php echo $Serveur_Config['lien_img_2'];?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
      </div>
      <div class="col-xs-6 col-sm-4">
          <p class="text-center"><img class="img-responsive img-rounded" src="http://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $Serveur_Config['img_3'];?>" alt="<?php echo $Serveur_Config['titre_img_3'];?>"></p>
          <p class="news"><?php echo $Serveur_Config['titre_img_3'];?></p>
          <p class="text-center bree-serif"><a class="align-right" href="<?php echo $Serveur_Config['lien_img_3'];?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
      </div>
    </div>
  </div>
  <br />
</div>
<div class="container">
<hr/>
  <div class="rows">
    <h1 class="text-center bree-serif">Notre équipe</h1><br>
      <?php
      global $staff;
      if (!empty($staff)){
        foreach ($staff as $staffs) {?>
          <div class="col-lg-4">
            <p class="text-center"><img class="rounded-circle" src="http://api.diamondcms.fr/face.php?id=<?php echo $Serveur_Config['id_cms'] . '&u=' . $staffs['pseudo']; ?>'&s=140" alt="Un membre du staff" width="140" height="140"></p>
            <h2 class="text-center"><?php echo $staffs['pseudo']; ?></h2>
            <?php if (!empty($staffs['staff_desc'])){ ?>
              <p class="text-center"><?php echo $staffs['staff_desc']; ?></p>
            <?php } ?>
          </div><!-- /.col-lg-4 -->
    <?php } }else { ?>
      <p>Aucun membre du staff n'a encore été enregistré !</p>
      <?php } ?>

  </div>
</div>
<br /><br />
