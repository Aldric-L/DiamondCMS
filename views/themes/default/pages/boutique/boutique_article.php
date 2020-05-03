<?php global $article, $reviews, $cant_by; ?>
<div id="fh5co-page-title" style="background-image: url(<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>views/uploads/img/<?php echo $Serveur_Config['bg']; ?>)">
  <div class="overlay"></div>
  <div class="text">
    <h1><a class="no" href="<?php echo $Serveur_Config['protocol']; ?>://<?php echo $_SERVER['HTTP_HOST'] . WEBROOT . 'boutique/' ?>">Boutique </a>-> <?php echo $article['name']; ?></h1>
  </div>
</div>
<br />
<!-- Page Content -->
<div class="container">

      <div class="row">

        <div class="col-lg-3">
          <!--<h1 class="my-4">Panier</h1>-->
          <div class="list-group">
            <a href="#" class="list-group-item active <?php if ($cant_by == 1 || $cant_by == 2){ ?> cant_buy <?php }else { ?> can_buy <?php } ?>" style="border-color: #197d62; background-color: #197d62; background-color: #197d62; font-family: Bree serif;"><span>Acheter l'article</span></a>
            <a href="<?php echo $Serveur_Config['protocol']; ?>://<?= $_SERVER['HTTP_HOST']; ?><?=WEBROOT; ?>boutique/getmoney/" class="list-group-item">Acheter des <?php echo $Serveur_Config['Serveur_money']; ?>s</a>
            <a href="<?php echo $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'boutique/'; ?>" class="list-group-item">Revenir à la liste des articles</a>
          </div>
        </div>
        <!-- /.col-lg-3 -->

        <div class="col-lg-9">

          <div class="card mt-4">
            <img class="card-img-top img-fluid" width="300" hight="300" src="<?= $article['link']; ?>" alt="">
            <div class="card-body">
              <h2 class="card-title"><?php echo $article['name']; ?></h2>
              <h4><span class="bree-serif">Prix : <strong><?php echo $article['prix']; ?> <?php echo $Serveur_Config['Serveur_money']; ?>(s)</strong></span> 
              <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])){ ?>
                <small>Vous disposez de <strong><?= $_SESSION['user']->getMoney(); ?> <?= $Serveur_Config['Serveur_money']; ?>(s)</strong></small>
              <?php } ?>
              </h4>
              <p class="card-text"><?php echo $article['description']; ?></p><br>
            </div>
          </div>
          <!-- /.card -->

        </div>
        <!-- /.col-lg-9 -->

      </div>

    </div>
    <!-- /.container -->
<style>
a.no {
  color: #197d62;
  text-decoration: none;
}
</style>
<?php if ($cant_by == 1 || $cant_by == 2){ ?>
  <div id="error_modal" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title text-center"> Erreur !</h3>
            </div>
            <div class="modal-body">
                <h3>Vous ne pouvez pas procéder à l'achat de cet article car 
                <?php if ($cant_by == 1){ ?>
                  vous n'êtes pas connecté !<h3>
                <?php }else if ($cant_by == 2){ ?>
                  <span style="color: red">vous n'avez pas assez de <?php echo $Serveur_Config['Serveur_money']; ?>s</span> ! <h3>
                <?Php } ?>
            </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success buy_money" data="">Acheter des <?php echo $Serveur_Config['Serveur_money']; ?>s</button>
          <button type="button" class="btn btn-default close_error_mod" data="">Fermer</button>
        </div>
      </div>
  </div>
</div>
<?php }else { ?>
<div id="buy_modal" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title text-center"> Achat de l'article</h3>
            </div>
            <div class="modal-body">
                <h4><span style="color: red;"><strong>Attention !</strong></span> Vous vous apprétez à passer commande d'un article sur notre boutique.</h4>
                <p>L'article est "<?php echo $article['name']; ?>" au prix de <?php echo $article['prix']; ?> <?php echo $Serveur_Config['Serveur_money']; ?>(s).</p>
                <br>
                <p style="color: red;"><em>Ce payement est un acte irréversible, en cliquant sur le bouton ci-après, vous acceptez nos conditions générales de vente et renoncez à votre droit de rétractation.</em></p>
            </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success buy" data="<?php echo $Serveur_Config['protocol'] . '://' . $_SERVER['HTTP_HOST'] . WEBROOT . 'boutique/buy/' . $article['id']; ?>">Acheter</button>
          <button type="button" class="btn btn-default close_buy_mod" data="">Fermer</button>
        </div>
      </div>
  </div>
</div>
<?php } ?>
