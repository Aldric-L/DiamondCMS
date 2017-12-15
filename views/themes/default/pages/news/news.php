<div id="fh5co-page-title">
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
                    <a href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'news/' ?><?php echo $n['id']; ?>" style="text-decoration: none; color: black;">
                        <h2 style="margin-left: 0%;"><?php echo $n['name']; ?> le <?php echo $n['date']; ?> par  <img width="32" height="32" src="http://api.diamondcms.fr/face.php?id=356a192b7913b04c54574d18c28d46e6395428ab&amp;u=<?php echo $n['user']; ?>&amp;s=32" alt="photo de profil de <?php echo $n['user']; ?>"> <?php echo $n['user']; ?></h2>
                        <p class="text-justify"><?php echo substr($n['content_new'], 0, 200);?></p>
                    </a>
                    <hr>
                    <?php } ?>
                </div>
                <br />
            <?php } ?> 
</div>
