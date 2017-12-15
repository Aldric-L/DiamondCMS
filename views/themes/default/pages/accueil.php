<?php global $servers, $n_serveurs, $news; ?>
<div id="fh5co-hero" style="background-image: url(images/hero_2.jpg)">
    <a href="#fh5co-main" class="smoothscroll animated bounce fh5co-arrow"><i class="ti-angle-down"></i></a>
    <div class="overlay"></div>
    <div class="container">
      <div class="col-md-8 col-md-offset-2">
        <div class="text">   
          <h1><strong class="bold"><?php echo $Serveur_Config['Serveur_name']; ?></strong><br /><?php echo $Serveur_Config['desc']; ?></h1>
        </div>
      </div>
    </div>
  </div>
  <!--<script type="text/javascript" src="http://localhost:8080/DiamondCMS/serveurs/json/1"></script>-->
  <script>
  var n_serveurs = <?php echo $n_serveurs; ?>;
  var lien_base = "<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>";
  //console.log(JSON.parse(data));
  $(document).ready(function(e) {
    $(".request_depend").hide();
    $.ajax({
      url: lien_base + "serveurs/json/"
    }).done(function( arg ) {
      //console.log(JSON.parse(arg));
      var json_result = JSON.parse(arg);
      console.log(json_result);
      $("#loader").hide();
      $(".request_depend").show();
      
      for (var i = 1; n_serveurs >= i; i++){
        $("#desc_serveur_".concat(i)).html(json_result[i-1]['Description']);
        $("#serveur_name_".concat(i)).html(json_result[i-1]['ServerName']);
        $("#img_serveur_".concat(i)).attr('src', lien_base+"views/uploads/img/" + json_result[i-1]['ImgDesc']);
        if (json_result[i-1]['Connect'] == false){
          $("#slots_serveur_".concat(i)).html('Slots : <span style="color: red;">Déconnecté</span>');
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: red;">Déconnecté</span>');
          $("#link_serveur_".concat(i)).attr('disabled', "");
        }else {
          $("#slots_serveur_".concat(i)).html('Slots : ' + json_result[i-1]['Players_n'] + " / " + json_result[i-1]['Slots']);
          $("#etat_serveur_".concat(i)).html('Etat du serveur : <span style="color: green;">Connecté</span>');
          $("#link_serveur_".concat(i)).attr('href', lien_base+"serveurs/" + i);
        }
        console.log(i);
      }
    })
  });
  </script>

  <?php if ($Serveur_Config['Accueil']['en_propa']){?>
  <br />
  <div id="infos">
    <div class="container">
        <div class="row">
          <div class="col-lg-4"><center>
            <h3><i class="fa-5x fa fa-<?php echo $Serveur_Config['Accueil']['fa_1'];?> " aria-hidden="true"></i></h3>
            <h2><?php echo $Serveur_Config['Accueil']['titre_1'];?></h2>
            <p><?php echo $Serveur_Config['Accueil']['desc_1'];?></p>
          </center></div><!-- /.col-lg-4 -->
          <div class="col-lg-4"><center>
            <h3 ><i class="fa-5x fa fa-<?php echo $Serveur_Config['Accueil']['fa_2'];?> " aria-hidden="true"></i></h3>
            <h2><?php echo $Serveur_Config['Accueil']['titre_2'];?></h2>
            <p><?php echo $Serveur_Config['Accueil']['desc_2'];?></p>
          </center></div><!-- /.col-lg-4 -->
          <div class="col-lg-4"><center>
            <h3><i class="fa-5x fa fa-<?php echo $Serveur_Config['Accueil']['fa_3'];?> " aria-hidden="true"></i></h3>
            <h2><?php echo $Serveur_Config['Accueil']['titre_3'];?></h2>
            <p><?php echo $Serveur_Config['Accueil']['desc_3'];?></p>
          </center></div><!-- /.col-lg-4 -->
        </div><!-- /.row -->
    </div>
  </div>
  <?php } ?>
  <hr>
<div class="servers">
  <div class="container">
    <div class="rows">
      <h1 class="text-center bree-serif">Etat du réseau <?php echo $Serveur_Config['Serveur_name']; ?></h1><br>
      <h3 id="loader" style="display: block;" class="text-center bree-serif"><img src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/ajax-loader.gif" alt="loading" /> Chargement en cours...</h5>
      <?php for ($i=1; $i <= $n_serveurs; $i++){
        if ($i % 2 == 0){?>
          <div class="col-md-2 request_depend"></div>
          <div class="col-md-4 request_depend"><center>
            <h2 class="text-center" id="serveur_name_<?php echo $i; ?>"></h2>
            <p id="desc_serveur_<?php echo $i; ?>"></p>
            <p id="slots_serveur_<?php echo $i; ?>"></p>
            <p id="etat_serveur_<?php echo $i; ?>"></p>
            <p><a id="link_serveur_<?php echo $i; ?>"class="btn btn-success acc" href="" role="button">Voir plus &raquo;</a></p>
          </center><br /></div>
          <div class="col-md-4 request_depend"><br /><img id="img_serveur_<?php echo $i; ?>" class="img-rounded img-centered" src="" alt="<?php echo $servers[$i-1]['Description'];?>"></div>
          <div class="col-md-2 request_depend"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div><br />
        <?php }else { ?>
          <div class="col-md-2 request_depend"></div>
          <div class="col-md-4 request_depend"><br /><img id="img_serveur_<?php echo $i; ?>" class="img-rounded img-centered" src="" alt="<?php echo $servers[$i-1]['Description'];?>"></div>
          <div class="col-md-4 request_depend"><center>
            <h2 class="text-center" id="serveur_name_<?php echo $i; ?>"></h2>
            <p id="desc_serveur_<?php echo $i; ?>"></p>
            <p id="slots_serveur_<?php echo $i; ?>"></p>
            <p id="etat_serveur_<?php echo $i; ?>"></p>
            <p><a id="link_serveur_<?php echo $i; ?>" class="btn btn-success acc" href="" role="button">Voir plus &raquo;</a></p>
          </center><br /></div>
          <div class="col-md-2 request_depend"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div><br />
      <?php } }?> 

    </div>
  </div>
</div>
<hr>
<div id="Staff">
  <div class="container">
  <hr/>
    <div class="rows">
      <h1 class="text-center bree-serif">Qui sommes nous ?</h1>
      <p class="text-center">Faites connaissance avec notre équipe de joueurs proffesionnels, près à vous aider en toute circonstance !</p>
        <br />
        <?php
        global $staff;
        if (!empty($staff)){
          foreach ($staff as $staffs) {?>
            <div class="col-sm-4">
              <p class="text-center"><img class="rounded-circle" src="http://api.diamondcms.fr/face.php?id=<?php echo $Serveur_Config['id_cms'] . '&u=' . $staffs['pseudo']; ?>&s=140" alt="Un membre du staff" width="140" height="140"></p>
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
</div>
<hr>
<div id="news">
  <div class="container">
  <hr>
    <!--<div class="row">
      <center><h2 class="text-danger">Dernières news sur le serveur :</h2><center>
    </div>-->
    <h1 class="text-center bree-serif">News !</h1>
    <p class="text-center">Decouvrez les nouveautés en rapport avec nos serveurs ! <a href="news/">Voir toutes les nouveautés...</a></p>
    <br /><br />
    <div class="row">
      <?php if (empty($news)){ ?>
        <p>Aucune news à afficher...</p>
      <?php }else {
        foreach ($news as $n){ ?>
          <p class="text-center"><img class="img-rounded" src="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'];?><?php echo WEBROOT;?>views/uploads/img/<?php echo $n['img'];?>" alt="<?php echo $n['name'];?>"></p>
          <p class="text-center news"><?php echo $n['name'];?></p>
          <p class="text-center bold">Le <?php echo $n['date'];?> par <?php echo $n['user'];?></p>
          <p class="text-center"><?php echo substr($n['content_new'], 0, 70);?>...</p>
          <p class="text-center bree-serif"><a href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'news/' ?><?php echo $n['id']; ?>"><button type="button" class="btn btn-primary acc">En savoir plus...</button></a></p>
        <?php }
       } ?> 
    </div>
  </div>
  <br />
</div>
<br /><br />
