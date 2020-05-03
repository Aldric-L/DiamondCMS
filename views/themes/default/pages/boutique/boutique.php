<?php global $l_articles, $n_articles_global, $cats; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1>Boutique</h1>
  </div>
</div>
<br />
<div id="explicboutique">
  <h1><?php echo $Serveur_Config['Serveur_name']; ?> vous propose une boutique.</h1>
  <p class="explicp">Cette boutique vous offre la possbilité d'acheter du contenu disponible sur notre serveur qui servira à compenser les dépenses liées au serveur, et ainsi vous offrir des parties de jeu de plus en plus confortables, et permettre le maintien du serveur dans le temps. <br />Merci de votre soutien !</p>
  <p id="red" class="text-danger"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  Attention, aucun remboursement ne sera effectué, comme précisé dans nos conditions génerales de vente.  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong></p>
</div>
<?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])){ ?>
<div class="container">
    <div class="rows">
        <div class="col-sm-4 col-lg-4 col-sm-offset-2 col-lg-offset-2">
            <p style="text-align: right;"><br><img class="" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>getprofileimg/<?php echo $_SESSION['user']->getPseudo(); ?>/110"></p>
        </div>
        <div class="col-sm-4 col-lg-4">
            <h3><?= $_SESSION['user']->getPseudo(); ?></h3>
            <p>Vous disposez de <strong><?= $_SESSION['user']->getMoney(); ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong></p>
            <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>boutique/getmoney/"><button class="btn btn-success btn-md" style="background-color: #197d62; border-color: #197d62;">Acheter des <?= $Serveur_Config['Serveur_money']; ?>s</button></a>
        </div>
        
    </div>
</div>
<?php }else { ?>
    <br />
<?php } ?>

<div class="container">
    <div class="row">
        <?php if (empty($l_articles)) { ?>
            <br><br>
        <center><P>Malheureusement, aucun article n'est pour le moment mis en vente.</P></center>
        <?php }else { ?>
            <div class="col-lg-12">
                <h3 class="page-header">Derniers articles ajoutés <small> - <?php echo $n_articles_global; ?> Articles </small></h3>
            </div>
            	<div class="col-md-12">
                    <div id="Carousel_main" class="carousel slide">
                        <ol class="carousel-indicators">
                            <li data-target="#Carousel_main" data-slide-to="0" class="active"></li>
                            <li data-target="#Carousel_main" data-slide-to="1"></li>
                        </ol>
                    <!-- Carousel items -->
                    <div class="carousel-inner">   
                        <div class="item active">
                	        <div class="row">
        <?php $i = 0; foreach ($l_articles as $key => $article){ $i++;?>
                                <div class="col-lg-4 col-sm-6 text-center">
                                    <img class="img-responsive img-center" src="<?php echo $article['link']; ?>" alt="">                                    
                                    <h3><?php echo $article['name']; ?>
                                        <small><?php echo $article['cat']; ?></small>
                                    </h3>
                                    <h4><span class="bree-serif">Prix : <strong><?php echo $article['prix']; ?> <?php echo $Serveur_Config['Serveur_money']; ?>(s)</strong></span> - <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>boutique/article/<?php echo $article['id']; ?>/">En savoir plus... </a></h4>
                                    <!--<p><?php echo $article['description']; ?></p>-->
                                </div>
                        <?php if ($i % 3 == 0 && $i != sizeof($l_articles)){ ?>
                            </div>
                        </div><!--.item-->
                                        
                        <div class="item">
                            <div class="row">
                        <?php }
        } //End foreach ?>
                    </div>
                </div><!--.item-->
                 
                </div><!--.carousel-inner-->
                  <a data-slide="prev" href="#Carousel_main" class="left carousel-control">‹</a>
                  <a data-slide="next" href="#Carousel_main" class="right carousel-control">›</a>
                </div><!--.Carousel-->
		    </div> <!-- .col-md-12 --></div>
            <br><br>
        <div class="rows">
    <?php } ?>
        <?php foreach ($cats as $key => $cat){ ?>
            <div class="col-lg-12">
                <h3 class="page-header"><?php echo $cat['name']; ?><small> - <?php echo $cat['nb_articles']; ?> Articles </small></h3>
            </div>
            <div class="col-md-12">
                <?php if (!empty($cat['articles'])){ ?>
                            <div id="Carousel_<?php echo str_replace(" ", "-", $cat['name']); ?>" class="carousel slide">
                                    <ol class="carousel-indicators">
                                        <li data-target="#Carousel_<?php echo str_replace(" ", "-", $cat['name']); ?>" data-slide-to="0" class="active"></li>
                                        <?php $i = $cat['nb_articles']; 
                                            $n = 0;
                                            while (true){
                                                if ($i-3 < 3 && $i > 3){
                                                    $n++;
                                                    echo '<li data-target="#Carousel_' . str_replace(" ", "-", $cat['name']) .'" data-slide-to="' . $n . '"></li>';
                                                    break;
                                                }else if ($i < 3) {
                                                    break;
                                                }else {
                                                    $n++;
                                                    $i = $i-3;
                                                    echo '<li data-target="#Carousel_' . str_replace(" ", "-", $cat['name']) .'" data-slide-to="'. $n . '"></li>';
                                                }
                                            } ?>
                                    </ol>
                                <!-- Carousel items -->
                                <div class="carousel-inner">   
                                    <div class="item active">
                                        <div class="row">
                                            <?php $i = 0; foreach ($cat['articles'] as $key => $article){ $i++;?>
                                            <div class="col-lg-4 col-sm-6 text-center">
                                                <!--<img class="img-circle img-responsive img-center" src="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/boutique/<?php echo $article['img']; ?>.png" alt="">
                                                --><img class="img-responsive img-center" src="<?php echo $article['link']; ?>" alt="">

                                                <h3><?php echo $article['name']; ?>
                                                    <small><?php echo $cat['name']; ?></small>
                                                </h3>
                                                <h4><span class="bree-serif">Prix : <strong><?php echo $article['prix']; ?> <?php echo $Serveur_Config['Serveur_money']; ?>(s)</strong></span> - <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>boutique/article/<?php echo $article['id']; ?>/">En savoir plus... </a></h4>
                                                <!--<p><?php echo $article['description']; ?></p>-->
                                            </div>
                                    <?php if ($i % 3 == 0){ ?>
                                        </div>
                                    </div><!--.item-->
                                                    
                                    <div class="item">
                                        <div class="row">
                                    <?php }
                                            } //End foreach ?>
                                </div>
                            </div><!--.item-->
                            
                            </div><!--.carousel-inner-->
                            <a data-slide="prev" href="#Carousel_<?php echo str_replace(" ", "-", $cat['name']); ?>" class="left carousel-control">‹</a>
                            <a data-slide="next" href="#Carousel_<?php echo str_replace(" ", "-", $cat['name']); ?>" class="right carousel-control">›</a>
                            </div><!--.Carousel-->
                <?php }else { ?>
                    <p>Aucun article enregistré dans cette catégorie.</p><br><br>
                <?php } ?>
                </div>
        <?php } // end foreach ?>
        </div>
    </div> <!-- .rows -->
</div><!-- .container -->