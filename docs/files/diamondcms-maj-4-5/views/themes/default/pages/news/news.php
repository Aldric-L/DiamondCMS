<?php global $news, $Serveur_Config; ?>
<div id="fh5co-page-title" style="background-image: url(<?= LINK; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>News du serveur</h1>
  </div>
</div>
<div class="container content-container">
    <h1 class="bree-serif">Liste des news du serveur :</h1><br />
        <?php global $news;
        if (empty($news)){ ?>
            <p>Aucune news à afficher...</p>
          <?php }else { ?>
                <div style="margin-left: 5%; margin-right: 2%;">
                  <hr>
                  <?Php  foreach ($news as $n){ ?>
                          <a href="<?php echo LINK . 'news/'; ?><?php echo $n['id']; ?>" style="text-decoration: none; color: black;">
                        
                        <?php if ($Serveur_Config['en_minecraft_profile']){ ?>
                          <h2 style="margin-left: 0%;"><?php echo $n['name']; ?> le <?php echo $n['date']; ?> par  <img width="32" height="32" src="<?php echo $Serveur_Config['api_url']; ?>face.php?id=356a192b7913b04c54574d18c28d46e6395428ab&amp;u=<?php echo $n['user']; ?>&amp;s=32" alt="photo de profil de <?php echo $n['user']; ?>"> <?php echo $n['user']; ?></h2>
                        <?php }else { ?>
                          <h2 style="margin-left: 0%;"><?php echo $n['name']; ?> le <?php echo $n['date']; ?> par  <img width="32" height="32" src="<?php echo LINK . 'views/uploads/img/'; ?><?php echo $n['img_profile']; ?>" alt="photo de profil de <?php echo $n['user']; ?>"> <?php echo $n['user']; ?></h2>
                          <?php } ?>
                        <p class="text-justify"><?php echo substr($n['content_new'], 0, 200);?></p>
                    </a>
                    <hr>
                    <?php } ?>
                </div>
                <br />
            <?php } ?> 
</div>
