<?php global $news, $Serveur_Config; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo Manager::makeGetImageLink($Serveur_Config['bg']); ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>News du serveur</h1>
  </div>
</div>
<div class="container content-container">
    <h1 class="title">Liste des news du serveur :</h1><br />
        <?php global $news;
        if (empty($news)){ ?>
            <p>Aucune news à afficher...</p>
          <?php }else { ?>
                  <?Php  foreach ($news as $k => $n){ ?>
                          <a href="<?php echo LINK . 'news/'; ?><?php echo $n['id']; ?>" style="text-decoration: none; color: black;">
                          <div class="container-fluid" >
                            <div class="rows">
                              <div class="col-lg-6">
                              <h2 style="margin-left: 0%;"><?php echo $n['name']; ?></h2>
                              </div>
                              <div class="col-lg-6">
                              <h5 class="text-right" style="margin-top: 15px;">le <?php echo $n['date']; ?> par   <img width="32" height="32" src="<?php echo LINK . 'views/uploads/img/'; ?><?php echo $n['img_profile']; ?>" alt="photo de profil de <?php echo $n['user']; ?>"> <?php echo $n['user']; ?></h5>
                              </div>
                            </div>
                            <?php if ($k != 0){ ?>
                              <div style="width: 98%; margin-left: 1%; border-bottom: 1px solid var(--main-title-color);"></div>

                            <?php } ?>
                          </div> 
                           
                        <!--<p class="text-justify"><?php echo substr($n['content_new'], 0, 200);?></p>-->
                    </a>
                    <?php } ?>
                <br />
            <?php } ?> 
</div>
