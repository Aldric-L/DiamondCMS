<?php global $servers, $n_serveurs, $news; ?>
<div id="fh5co-hero" >
    <div class="overlay" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)"></div>
    <div class="container">
      <div class="col-md-8 col-md-offset-2">
        <div class="text wow fadeInUp " data-wow-duration="2s" data-wow-delay="0.2s" >   
          <h1><strong class="bold"><?php echo $Serveur_Config['Serveur_name']; ?></strong><br /><?php echo $Serveur_Config['desc']; ?></h1>
        </div>
      </div>
    </div>
  </div>
  <?php if ($Serveur_Config['Accueil']['en_propa']){?>
  <br />
  <div id="infos">
    <div class="container">
        <div class="row">
          <div class="col-lg-4 wow fadeInLeft" data-wow-delay="0.2s" ><center>
          <h3>
            <?php if ($Serveur_Config['Accueil']['img_1'] == "fa") { ?>
              <i class="fa-5x fa fa-<?php echo $Serveur_Config['Accueil']['fa_1'];?> " aria-hidden="true"></i>
            <?php }else { ?>
              <img width="120px" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?= $Serveur_Config['Accueil']['img_1']; ?>" alt="">
            <?php } ?>
          </h3>            
          <h2><?php echo $Serveur_Config['Accueil']['titre_1'];?></h2>
            <p><?php echo $Serveur_Config['Accueil']['desc_1'];?></p>
          </center></div><!-- /.col-lg-4 -->
          <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.2s" ><center>
          <h3>
            <?php if ($Serveur_Config['Accueil']['img_2'] == "fa") { ?>
              <i class="fa-5x fa fa-<?php echo $Serveur_Config['Accueil']['fa_2'];?> " aria-hidden="true"></i>
            <?php }else { ?>
              <img width="120px" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?= $Serveur_Config['Accueil']['img_2']; ?>" alt="">
            <?php } ?>
          </h3>    
            <h2><?php echo $Serveur_Config['Accueil']['titre_2'];?></h2>
            <p><?php echo $Serveur_Config['Accueil']['desc_2'];?></p>
          </center></div><!-- /.col-lg-4 -->
          <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.2s" ><center>
          <h3>
            <?php if ($Serveur_Config['Accueil']['img_3'] == "fa") { ?>
              <i class="fa-5x fa fa-<?php echo $Serveur_Config['Accueil']['fa_3'];?> " aria-hidden="true"></i>
            <?php }else { ?>
              <img width="120px" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?= $Serveur_Config['Accueil']['img_3']; ?>" alt="">
            <?php } ?>
          </h3>                <h2><?php echo $Serveur_Config['Accueil']['titre_3'];?></h2>
            <p><?php echo $Serveur_Config['Accueil']['desc_3'];?></p>
          </center></div><!-- /.col-lg-4 -->
        </div><!-- /.row -->
    </div>
  </div>
  <?php } ?>
  <hr>
<?php if (defined("DServerLink") && DServerLink && !empty($n_serveurs)){ ?>
<div class="servers">
  <div class="container">
    <div class="rows">
      <p style="display:none;" id="infos-servers" data-link="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>" data-nb="<?php echo $n_serveurs; ?>"></p>
      <h1 class="text-center bree-serif">Etat du réseau <?php echo $Serveur_Config['Serveur_name']; ?></h1><br>
      <h3 id="loader" style="display: block;" class="text-center bree-serif"><img src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/ajax-loader.gif" alt="loading" /> Chargement en cours...</h5>
      <?php for ($i=1; $i <= $n_serveurs; $i++){
        if ($i % 2 == 0){?>
          <div class="col-sm-2 request_depend"></div>
          <div class="col-sm-4 request_depend"><center>
            <h2 class="text-center" id="serveur_name_<?php echo $i; ?>"></h2>
            <p id="desc_serveur_<?php echo $i; ?>"></p>
            <p id="slots_serveur_<?php echo $i; ?>"></p>
            <p id="etat_serveur_<?php echo $i; ?>"></p>
            <p><a id="link_serveur_<?php echo $i; ?>"class="btn btn-success acc" href="" role="button">Voir plus &raquo;</a></p>
          </center><br /></div>
          <div class="col-sm-4 request_depend"><br /><img id="img_serveur_<?php echo $i; ?>" class="img-rounded img-centered" src="" alt=""></div>
          <div class="col-sm-2 request_depend"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div><br />
        <?php }else { ?>
          <div class="col-sm-2 request_depend"></div>
          <div class="col-sm-4 request_depend"><br /><img id="img_serveur_<?php echo $i; ?>" class="img-rounded img-centered" src="" alt=""></div>
          <div class="col-sm-4 request_depend"><center>
            <h2 class="text-center" id="serveur_name_<?php echo $i; ?>"></h2>
            <p id="desc_serveur_<?php echo $i; ?>"></p>
            <p id="slots_serveur_<?php echo $i; ?>"></p>
            <p id="etat_serveur_<?php echo $i; ?>"></p>
            <p><a id="link_serveur_<?php echo $i; ?>" class="btn btn-success acc" href="" role="button">Voir plus &raquo;</a></p>
          </center><br /></div>
          <div class="col-sm-2 request_depend"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div><br />
      <?php } }?> 

    </div>
  </div>
</div>
<hr>
<?php } ?>
<div id="Staff" class="wow">
  <div class="container">
  <hr/>
    <div class="rows">
      <h1 class="text-center bree-serif">Qui sommes nous ?</h1>
      <p class="text-center">Faites connaissance avec notre équipe de joueurs proffesionnels, prêts à vous aider en toute circonstance !</p>
        <br />
        <?php
        global $staff;
        if (!empty($staff)){
          $i = 0;
          foreach ($staff as $staffs) {?>
            <div class="col-sm-3">
              <p class="text-center"><img class="rounded-circle" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>getprofileimg/<?php echo $staffs['pseudo']; ?>/140" alt="Un membre du staff" width="140" height="140"></p>
              <h2 class="text-center"><?php echo $staffs['pseudo']; ?></h2>
              <p class="text-center"><?php echo $staffs['role_name']; ?></p>
              <p class="text-center bree-serif"><a href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'compte/' ?><?php echo $staffs['pseudo']; ?>"><button type="button" class="btn btn-primary acc">Voir le profil</button></a></p>
            </div><!-- /.col-lg-4 -->
            <?php $i = $i+1;
            if ($i == 4){ $i=0; ?>
              <div class="col-sm-12"><br><br></div>
              <?php }
       } }else { ?>
        <p>Aucun membre du staff n'a encore été enregistré !</p>
        <?php } ?>
    </div>
  </div>
</div>
<hr>
<div id="news">
  <div class="container">
    <hr>
    <h1 class="text-center bree-serif">News !</h1>
    <p class="text-center">Decouvrez les nouveautés en rapport avec nos serveurs ! <a href="news/">Voir toutes les nouveautés...</a></p>
    <br />
    <div class="row">
      <?php if (empty($news)){ ?>
        <p style="text-align: center;">Aucune news n'est à afficher.</p>
      <?php }else { ?>
        <br />
        <?php foreach ($news as $n){ ?>
        
        <div class="col-sm-4">
        <?php if ($n['img'] != "noimg") { ?>
          <p class="text-center"><img style="width: 400px" class="img-rounded" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $n['img'];?>" alt="<?php echo $n['name'];?>"></p>
          <?php }else { ?>
        <p class="text-center" style="font-size: 150px; color: black;"><span>
          <i class="fa fa-info"></i>
        </span><p>
        <?php } ?>
          
          <p class="text-center news"><?php echo $n['name'];?></p>
          <p class="text-center bold">Le <?php echo $n['date'];?> par <?php echo $n['user'];?></p>
          <p class="text-center bree-serif"><a href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'news/' ?><?php echo $n['id']; ?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
          </div>
        <?php }
       } ?> 
    </div>
    <div class="col-md-12">
  </div>
  </div>
  <br />
</div>
<br /><br />